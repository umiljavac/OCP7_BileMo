# OCP7_BileMo
Seventh project of OpenClassrooms "Développeur d'application PHP/Symfony" cursus. 

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/1d9cae87-686c-4bf4-9a4d-e84efd7b996a/mini.png)](https://insight.sensiolabs.com/projects/1d9cae87-686c-4bf4-9a4d-e84efd7b996a)

## 1-Intro 
The aim of this project is to create a B2B ecommerce of cellphones by providing to clients an API REST.  
So we have to reach the fourth level of Richardson's model. 
  
## 2-Requirements
This project use Symfony 4 framework and Symfony 4 requires PHP version > 7.1.3 to run. 

## 3-Bundles 
In addition to the Symfony framework this project uses several bundles:
* [JMSSerializerBundle](https://github.com/schmittjoh/JMSSerializerBundle)
* [FOSRestBundle](https://github.com/FriendsOfSymfony/FOSRestBundle)
* [LexikJWTAuthenticationBundle](https://github.com/lexik/LexikJWTAuthenticationBundle)
* [WillDurand/Hateoas](https://github.com/willdurand/Hateoas)
* [NelmioApiDocBundle](https://github.com/nelmio/NelmioApiDocBundle)
* [WhiteOctoberPagerfantaBundle](https://github.com/whiteoctober/WhiteOctoberPagerfantaBundle)
* and [Guzzle](https://github.com/guzzle/guzzle)

## 4-Installation 
1. Clone this repository (Master branche)
2. Put the downloaded repository into your server root folder. You can also use the Symfony server (excellent choice), in this case you don't have to put the dowloaded repository in your root server folder, but after complete installation you will have to run the `$ php bin/console server:run` command.
3. Install the vendors : 
  * Download [composer](https://getcomposer.org/)
  * Put the composer.phar file into the root folder of the downloaded repository.
  * Then run `$ php composer.phar update`
  * Now all the vendors are installed.
4. Set the database :
  * In .env file customize the line :
  `DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name"`
  * You may also have to configure the config/packages/doctrine.yaml file for adjust your MySQL version.
  * Create the database `$ php bin/console doctrine:database:create`
  * Create all the tables 
    * `$ php bin/console doctrine:migrations:diff`  
    * `$ php bin/console doctrine:migrations:migrate`
 5. Configure the JWT authentication :
   * Generate the SSH keys : 
     * `$ mkdir -p config/jwt` For Symfony3+, no need of the -p option
     * `$ openssl genrsa -out config/jwt/private.pem -aes256 4096`
     * `$ openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem`
   * You may also have to adjust your config parameters :
     * Please refer to [LexikJWTAuthenticationBundle-doc-configuration](https://github.com/lexik/LexikJWTAuthenticationBundle/blob/master/Resources/doc/index.md#configuration)
 6. Optional :
* Just after installation, you can fill the database with a set of data examples allready written in the Datafixtures folder. 
* Fill the database with the data set example `$ php bin/console doctrine:fixtures:load` press `y`.
* You can also use `$ php bin\console app:fixturesReload` command to achieve this. 

## 5-Documentation
* The Api comes with a documentation created with the NelmioApiDocBundle. 
* To read it, go to "localhost:yourServerPort/api/doc" on your web browser.
## That's it !   
