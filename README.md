# Php framework - DBM
This project will consist in developping a REST API with Symfony and automate the deployment with Ansible.

## Members
* Yves Molina
* Jonathan Diouf
* Kolia Bernheim

## Tools
Jira : [DBM board](https://kolia.atlassian.net/jira/software/projects/DBM/boards/1)

## Git
Branches : 
* main
* dev
* feature/...

## API REST
### User
* registration of user **/api/register**
* connection of user, retrieving the authentication token **/api/login**
* update current user information **/api/user** – AUTHED
* display current user information **/api/user** – AUTHED
### Products
* Retrieve list of products **/api/products**
* Retrieve information on a specific product **/api/product/{productId}**
* Add a product **api/product** – AUTHED
* Modify and delete a product **/api/product/{productId}** – AUTHED
### Cart
* Add a product to the shopping cart **/api/cart/{productId}** – AUTHED
* Remove a product to the shopping cart **/api/cart/{productId}** – AUTHED
* State of the shopping cart (list of products in the cart) **/api/cart** – AUTHED
* Validation of the cart (aka converting the cart to an order) **/api/cart/validate** – AUTHED
### Orders
* recover all orders of the current user **/api/orders/** – AUTHED
* Get information about a specific order **/api/order/{orderId}** – AUTHED
-> Is only authorized if the order belong to the logged user.


### Commands
```Powershell
#create project:
symfony new api
#install dependencies
cd api/
composer install
#add package:
composer require <package_name>
#start web server:
symfony server:start | symfony serve -d --no-tls
#create entity | add fields to existiong entity
php bin/console make:entity
#DB operations
php bin/console doctrine:database:create
php bin/console make:migration
php bin/console doctrine:migrations:migrate
-> see fixtures to populate the database
#clear all cache
php bin/console cache:pool:clear cache.global_clearer
```

### Librairies 
* symfony/maker-bundle --dev
* api-platform
* lexik/jwt-authentication-bundle
