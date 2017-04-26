#Test S3Upload

In order to test S3Upload I have to figure what I do need to test and what I don’t need to test. Part of that is possibly going to be redesigning the classes so that there is as little as possible to test. Also, I’ll need a way to isolate all of the calls made to external resources like the hard drive or the Internet as can be seen in “Dependencies and their external calls”.

Figure “Dependencies and their external calls”
![Dependencies and their external calls](uml/s3_upload_original_deps.png "Dependencies and their external calls")

I broke the external calls out into their classes. I put fopen in a class called FIleUtility so that I can inject it and replace it for testing. I also moved the S3Client into a service provider. I’m going to use a service provider for the S3Client so it can be re-used across the application if needed. I’ve outlined these additional dependencies in the Figure “Testable S3UploadService”. It really adds a lot of classes.

Figure “Testable S3UploadService”
![Testable S3UploadService](uml/s3_upload_testable.png "Dependencies and their external calls")
￼
 I went from 6 classes to 9 classes when I broke out the external calls into their own classes. Now if I add tests to this I get Figure “Testing the Application Logic”. This adds two additional tests plus phpunit and Mockery.

Figure “Testing the Application Logic”
![Testable the Application Logic](uml/s3_upload_testable_with_tests.png "Testing the Application Logic")
￼

