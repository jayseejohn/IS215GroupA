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
          var errorMessage = response.error;
          
          if (response.error == 'Image contains unsafe content') {
            var list = '';
            list += '<ul class="list-unstyled">';
            $.each(response.flaggedLabels, function (key, value) {
              list += `<li>
                          Content: <b>${value.name}</b> 
                          Confidence: <b>${value.confidence}</b>
                      </li>`;
            });
            list += '</ul>';
            
            errorMessage = `${response.error} <br /> <br />${list}`;
          }

          customAlert('error', "Oops!", '', errorMessage);
          hideLoader();
          return;
        }

        console.log(response);

        const labels = response.labels.map(label => label.Name);
        const fileName = response.fileName;
        const imageUrl = response.imageUrl;
        let generatedLabels = `${labels.join(', ')}`;

        console.log(labels);
        console.log(labels.length);

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
          imageUrl: imageUrl,
          labels: labels
        };
        
        // Store to local storage with 1hr expiration
        var expiration = 3600000;
        storeToLocalStorage(fileName, value, expiration);
        var tags = getTags(labels);

        var messageHtml = `
            <img src="${imageUrl}" class="w-100" alt="${fileName}" />
            <h3 class="text-center my-5"><b>${messageContent.title}</b></h3>
            <p class="text-center">${tags}</p>
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

const loadFromLocalStorage = (key) => {
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

const loadAllStoredArticles = () => {
  var articles = getAllLocalStorageItems();

  if (articles !== null) {
    $('#generated-articles').html('');
    $.each(articles, function (key, values) {
        let item = loadFromLocalStorage(key);
        console.log(item);
        
        if (item === null) {
          return false;
        }
        var labels = null;
        if (item.labels !== undefined && item.labels.length > 0) {
          labels = item.labels;
        }

        var tags = getTags(labels);

        var content = {
          imageUrl: item.imageUrl,
          fileName: item.fileName,
          title: item.title.substring(0, 50)+'...',
          tags: tags,
          article: item.article.substring(0, 250)
        };
        
        var articleBody = getArticleBody(content);

        var html = `
          <div class="col-md-4">
            <div class="card article-card mt-3 articles-container"
              data-image="${item.imageUrl}"
              data-file_name="${item.fileName}"
              data-title="${item.title}"
              data-article="${item.article}"
              data-labels="${labels}"
            >
              ${articleBody}
            </div>
          </div>
        `;

        $('#generated-articles').append(html);
    });
  }
}

const getTags = (labels) => {
  var tags = '';
  if (labels !== null) {
    $.each(labels, function (index, label) {
      tags += `<span class="badge badge-primary mr-1">${label}</span>`;
    });
  }
  return tags;
}

const getArticleBody = (content) => {
  return `<div class="col-md-12 p-0">
            <img src="${content.imageUrl}" class="card-img-top object-fit-cover" alt="${content.fileName}">
          </div>
          <div class="card-body p-0">
            <h5 class="card-title m-0 p-3 text-center">
              <b>${content.title}</b>
            </h5>
            <p class="tags text-center">${content.tags}</p>
            <p class="card-text bg-light p-4 border article-content">
              ${content.article}
            </p>
          </div>`;
}


$(function (){
    $('#generated-articles').html(`
      <div class="container alert alert-info p-5 text-center">
        No generated articles found. Go to <a href="/index.php">upload</a> page first.
      </div>
    `);

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

    $(document).on('click', '.articles-container', function () {
      $('#loader-container').addClass('d-none');
      $('#modal #articles-container').removeClass('d-none');

      var targetElement = $('#articles-modal-container');
      var imageUrl = $(this).data('image');
      var fileName = $(this).data('file_name');
      var title = $(this).data('title');
      var article = $(this).data('article');
      var labels = $(this).data('labels');
      labels = labels.split(',');

      var tags = getTags(labels);

      var content = {
        imageUrl: imageUrl,
        fileName: fileName,
        title: title,
        tags: tags,
        article: article
      };

      var articleBody = getArticleBody(content);
      
      targetElement.html('');
      var html = `<div class="card mt-3">${articleBody}</div>`;
      targetElement.html(html);
      $('#modal').modal('toggle');

    });
})
