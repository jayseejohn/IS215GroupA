const showLoader = (message) => {
  $('#upload-btn').attr('disabled', true).addClass('pe-none');
  $('#loader-container').removeClass('d-none');
  $('#modal').modal('toggle');
  $('#loader-prompt').html(message);
  $('#modal #articles-container').addClass('d-none');
}

const hideLoader = () => {
  setTimeout(function () {
    $('.close-modal').trigger('click');
  }, 500);
  $('#upload-btn').attr('disabled', false).removeClass('pe-none');
  $('#loader-container').addClass('d-none');
  $('#loader-prompt').html('');
}

const showArticle = () => {
  $('#loader-container').addClass('d-none');
  $('#modal #articles-container').removeClass('d-none');
}

const customAlert = (type, title, text, html = '') => {
    Swal.fire({
      title: title,
      text: text,
      html: html,
      icon: type
    });
}

const getPrompt = (labels) => {
  let prompt = "";

  prompt += 'I uploaded an image that contains the following elements from Amazon Rekognition: ' + labels.join(', ');
  prompt += 'Please write a short, engaging, news article describing the scene in an informative way.';
  prompt += 'Your response must be raw and in a valid json format only without any other extra values or "json" at the start of your response. The json response must have 2 keys, title and article_content.';
  prompt += 'The value of the title key must be the title related to the news article.';
  prompt += 'With regards to the article content value, feel free to add emphasis to certain portions in the news article';
  prompt += 'using html tags like <b> html tags. Only use html tags and do not use markdown tags like asterisks for emphasizing certain portions.';
  prompt += "Feel free to be creative but the article's title and content must be closely related to the elements described from Amazon Rekognition.";

  return prompt;
}

const previewImage = (e) => {
  var file = e.target.files[0];
  var preview = document.getElementById('preview');
  preview.src = "";
  if (file === undefined) {
    return;
  }
  

  var fileName = file.name;
  var ext = fileName.substring(fileName.lastIndexOf('.') + 1).toLowerCase();
  if ((ext == "gif" || ext == "png" || ext == "jpeg" || ext == "jpg")) {
    preview.src = URL.createObjectURL(file);
    preview.onload = function() {
      URL.revokeObjectURL(preview.src)
    }
  }
}

const processImage = (formData) => {
  try {

    $.ajax({
      url: 'process-image.php',
      method: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      cache: false,
      dataType: 'json',
      beforeSend: function () {
        showLoader("Processing your image, please wait...");
      },
      success: function (response) {
        if (response.message !== undefined) {
          customAlert('error', "Oops!", '', response.message);
          hideLoader();
          return;
        }

        if (response.error !== undefined) {
          customAlert('error', "Oops!", '', response.error);
          hideLoader();
          return;
        }

        console.log(response);

        const labels = response.labels.map(label => label.Name);
        const fileName = response.fileName;
        const imageUrl = response.imageUrl;
        let generatedLabels = `${labels.join(', ')}`;

        $('#labels-container').after(generatedLabels);
        generateArticle(labels, fileName, imageUrl);
      },
      error: function (response) {
        console.log(response);
        customAlert('error', `Error fetching response`, '', response.responseText);
        hideLoader();
      }
    });

  } catch (error) {
    console.log(error);
    customAlert('error', 'Unexpected error occured', error);
    hideLoader();
  }
}

const generateArticle = (labels, fileName, imageUrl) => {
  let prompt = getPrompt(labels);
  let messages = [
          { role: 'system', content: 'You are a creative and brilliant writer that turns image data into news articles.' },
          { role: 'user', content: prompt }
      ];
  
  let generatedLabels = `${labels.join(', ')}`;

  try {
    $.ajax({
      url: 'openai.php',
      method: 'POST',
      data: {
        messages: messages,
        labels: generatedLabels,
        file_name: fileName
      },
      dataType: 'json',
      beforeSend: function () {
        $('#loader-prompt').html("Image has been successfully uploaded. <br /> Generating your article with OpenAI");
      },
      success: function (response) {
        console.log(response);
        if (response.message !== undefined) {
          customAlert('error', "Oops!", response.message, '');
          hideLoader();
          return;
        }

        if (response.choices[0].message.content === undefined) {
            customAlert('error', "We're sorry", "Open AI was not able to generate an article.", '');
            hideLoader();
            return;
        }

        var messageContent = '';
        messageContent = response.choices[0].message.content;
        messageContent = JSON.parse(messageContent);
        
        var value = {
          fileName: fileName,
          title: messageContent.title,
          article: messageContent.article_content,
          imageUrl: imageUrl
        };

        var expiration = 3600000; // Test for 1hr
        
        // Store to local storage
        storeToLocalStorage(fileName, value, expiration);

        var messageHtml = `
            <img src="${imageUrl}" class="w-100" alt="${fileName}" />
            <h3 class="text-center my-5"><b>${messageContent.title}</b></h3>
            <div class="container bg-light p-3 border rounded-4">${messageContent.article_content}</div>
        `;
        
        messageContent = messageHtml;

        $('#articles-modal-container').html(messageContent);
        
        showArticle();
        $('#upload-btn').attr('disabled', false).removeClass('pe-none');
        loadAllStoredArticles();
        $('#form')[0].reset();
      },
      error: function (response) {
        console.log(response);
        customAlert('error', `Error fetching response`, '', response.responseText);
        hideLoader();
      }
    });

  } catch (error) {
    console.log(error);
    customAlert('error', 'Unexpected error occured', error);
    hideLoader();
  }
}

const storeToLocalStorage = (key, value, expiration) => {
  let now = new Date();
  let item = {
    value: value,
    expiry: (now.getTime() + expiration),
  }
  localStorage.setItem(key, JSON.stringify(item));
}

const getAllLocalStorageItems = () => {
  var items = {};
  
  if (localStorage.length < 1) {
    return null;
  }

  for (let i = 0; i < localStorage.length; i++) {
    var key = localStorage.key(i);
    items[key] = localStorage.getItem(key);
  }

  return items;
}

function loadFromLocalStorage(key) {
  var values = localStorage.getItem(key);
  if (!values) {
    return null
  }

  var item = JSON.parse(values);
  var now = new Date();
  if (now.getTime() > item.expiry) {
    localStorage.removeItem(key);
    return null
  }

  return item.value;
}

function loadAllStoredArticles() {
  var articles = getAllLocalStorageItems();

  $('#generated-articles').html('');
  if (articles !== null) {
    $.each(articles, function (key, values) {
        let item = loadFromLocalStorage(key);
        var html = `
          <div class="col-md-4">
            <div class="card mt-3">
              <div class="col-md-12 p-0" id="article-image-container">
                <img src="${item.imageUrl}" class="card-img-top" alt="${item.fileName}">
              </div>
              <div class="card-body p-0">
                <h5 class="card-title m-0 p-3">
                  <b>${item.title.substring(0, 50)}...</b>
                </h5>
                <p class="card-text bg-light p-4 border">
                  ${item.article.substring(0, 250)}...
                </p>
              </div>
            </div>
          </div>
        `;

        $('#generated-articles').append(html);
    });
  }
}


$(function (){
    // localStorage.clear();
    loadAllStoredArticles();

    $('#form').on('submit', function(e) {    
        e.preventDefault();
        e.stopPropagation();
        let formData = new FormData();
        let file = $('#file')[0].files[0];

        if (!file) {
          return;
        }

        if ((file.size / 1048576) > 5) {

          let html = `<b>${file.name}</b> is too large. 
                      <br /><br />Uploaded File's Size: <b>${parseFloat(file.size / 1048576).toFixed(2)} MB</b>. 
                      <br /><br />Image must not exceed 5MB of size.`;

          customAlert('error', "I'm sorry", '', html);
          hideLoader();
          return;  
        }
        
        formData.append('file', file);
        processImage(formData);
    });
})
