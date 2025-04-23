# SETUP S3 Bucket, AWS Lambda, and API Gateway

### For this setup, you need an API Gateway to communicate to the AWS Lambda

  

**A. You must create an S3 bucket and a Lambda function first.**

  

1. Follow the previous activity's setup guide (Activity 4 Guide) on how to create an S3 bucket and a Lambda function.

---

*NOTE: Instead of only choosing `PUT` method under Event types (Step 3.6.), you must also tick (check) `Multipart upload` event type*

  

---

2. Take note of the function name.

3. Make sure `BUCKET` and `URL` environment variables are connected to the S3

bucket and are set properly to your Lambda function.

4. After creating the Lambda function, upload the provided zip file `lambda.zip` under Code tab.

  

##### B. Setup your Custom AWS API Gateway

1. Go to your AWS account.

2. Search **API Gateway** on AWS Console to navigate to API Gateway page.

3. On the left sidebar, click **APIs** option to navigate to APIs page.

4. Create a new API, click **Create API** located on the upper right part of the screen.

5. Find **Rest API** option. Click **Build** button.

a. API Details: `New API`

b. API Name: *Add any API name. eg. `CustomAPI`*

c. IP address type: `IPv4`

d. Click `Create API`


6. After creating your API, you will be redirected back to **Resources page**. You need to *create a new Resource and Method*.


1. Create Resource


a. On the Resources page, click **Create resource** button.


b. Resource name: *eg. process-uploaded-image*


c. Click **Create resource**


2. Create Method:


a. Click the newly created resource. Under `Methods` container, click **Create method**.


b. Method details: `POST`


c. Integration type: `Lambda function`


d. Turn on the `Lambda proxy integration`


e. Lambda function: *Select the function you created in Step 2A*.


f. Click **Create method**


7. Click **API Settings** on the sidebar menu. Scroll down to Binary media types section, click **Manage media types** then the following types: `multipart/form-data` and `*/*`

  

8. After creating a method, go back to **Resources** page. Click the resource you created on 6a. On the upper right part, click **Deploy API**.

a. Under Deploy API modal.

b. Stage dropdown: `New stage`

c. Stage name: *eg. `project`*

9. Verify your setup. Go back to Lambda page. Under function overview, a new API Gateway trigger should be created.

a. Click **API Gateway**

b. Click **Configuration tab** > **Triggers**. Under Triggers, you should see the API Gateway trigger and an API endpoint similar to this `https://4nmgqp7rhi.execute-api.us-east-1.amazonaws.com/project/process-uploaded-image`. You need this endpoint to communicate from your machine to AWS Lambda function for this project. **Check the sample image for reference**.

  

You can now upload the `lambda.zip` file to your created AWS lambda function, under **Code** tab.

  
  

#### CURL REQUEST METHOD

---

**URL** : `Your API Gateway`

  

**Request Method**: `POST`

  

**Request Body**:

Parameters:


`file` => `File` object. *You can use PHP **[CURLFile()](https://www.php.net/manual/en/class.curlfile.php)** class for this.*,


`filename` => `Name of the file`,


`contentType` => *`(eg. images/jpg, images/png)`*


  

**Success Response (200 OK)**

{

*fileName: `File name of the uploaded file`*,

*labels: `Raw response from Amazon Rekognition label recognition`*,

*imageUrl: `Pre-signed URL of the uploaded image to view image from private S3 bucket`*

}

  

**Error Response (400 Bad Request)**


{


*error: No file uploaded*


}

  

**Error Response (500 Internal Server Error)**


{


*error: Failed to process image*


}

  

---