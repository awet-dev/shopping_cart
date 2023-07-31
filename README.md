# E-commerce System Problem-Solving and PHP Microservice

This repository contains solutions for two tasks related to an e-commerce system: problem-solving for a checkout issue and the development of a PHP microservice to handle promotions and discounts.

## Task 1: Problem-Solving for Checkout Issue

### Context

The client's e-commerce system in PHP, using a PHP framework, has encountered an issue during the checkout process. After clicking through to the checkout page, they are presented with a blank page without any header or footer, and no error message is displayed.

### Steps to Address the Issue

1. **Inspect the Codebase**: Review the relevant PHP files to identify potential issues related to the checkout process.

2. **Enable Error Reporting**: Ensure that PHP error reporting is enabled to capture any errors or exceptions during checkout.

3. **Debugging**: Use debug statements or debugging tools to log relevant information during the checkout process.

4. **Identify the Issue**: Analyze the debug logs and error messages to pinpoint the exact location and nature of the problem.

5. **Address the Issue**: Make the necessary corrections in the code to fix the problem and ensure the checkout page is displayed correctly.

6. **Test the Fix**: Perform thorough testing to verify that the issue has been resolved and the checkout page functions as expected.

## Task 2: Development of PHP Microservice

### Context

The client wants to give a promotion of 1 for free when customer buy 2  for some items in the catalog and a 20% discount on certain other items in their custom e-commerce system.

### Requirements

- In the standard flow the e-commerce application will send the shopping cart data to the PHP microservice via HTTP requests in a specific format.
- The microservice should return a corrected shopping cart with adjusted totals and indications of applied discounts.
- The microservice will be be able to apply promations and discounts if such rule exist in the data that is recieved.
- But for now the microservice will be using hard coded data of product to process the flow.

### Steps to Develop the Microservice

1. **Choose Tools and Frameworks**: I have chosen Laravel to build the PHP microservice.

2. **Instruction**: 
    - To start the development server, run:
        ```bash
        php artisan serve
        ```
    - Api end-point for the shopping cart:
        For testing the cart data calculations, you can use [Postman](https://www.postman.com/) to make API requests and check the results interactively.
        ```bash
        http://127.0.0.1:8000/shopping-cart
        ```
    - Product Payload
        | id  | name          | quantity | promotion | discount | price | vat |
        |---- |-------------- |--------- |---------- |-------- |----- |---- |
        | 1   | product one   | 3        | 1/2       | 10       | 43    | 12  |
        | 2   | product two   | 2        | -         | 6        | 56    | 10  |
        | 3   | product three | 5        | 1/4       | 0        | 33    | 10  |
            
    - Return data is: 
        ## Cart Data

        | Product ID | Name          | Price | Promotion | Discount % | VAT Rate | Quantity | Subtotal | Discount Amount | Promotion Amount | VAT Amount |
        |------------|---------------|-------|-----------|------------|----------|----------|----------|-----------------|------------------|------------|
        | 1          | product one   | $43   | 0.5       | 10%        | 12%      | 3        | $73.10   | $12.90          | $43.00           | $8.77      |
        | 2          | product two   | $56   | -         | 6%         | 10%      | 2        | $105.28  | $6.72           | -                | $10.53     |
        | 3          | product three | $33   | 0.25      | 0%         | 10%      | 5        | $132.00  | $0.00           | $33.00           | $13.20     |

        **Final Total:** $342.88

        **Note:** The "Promotion" value represents a ratio of free to bought quantity. For example, a promotion value of 0.5 means you get one item free for every two items purchased.






