[![Build Status](https://travis-ci.org/prufrock/flowerbug.svg?branch=master)](https://travis-ci.org/prufrock/flowerbug)

# About flowerbug-admin
The flowerbug-admim is a site built to administer a shops that sells digital goods.


- [aws-sdk-php v2](http://docs.aws.amazon.com/aws-sdk-php/v2/guide/).

## Purchasing Projects.

1. The application receives a message from a payment processor.
1. The message is validated with the payment processor.
1. A valid message is converted to an order.
1. The order is saved.
1. The order is delivered to the customer.

What happens when verification is not succesful?
Then the server returns an error to the payment processor that posted the message to it because I want the message to be sent again.

