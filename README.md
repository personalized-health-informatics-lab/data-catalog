# Digital Commons

Welcome to the Digital Commons project. Our aim is to encourage the sharing and reuse of research data among insitutions and individuals by providing a simple yet powerful search platform to expose existing datasets to the researchers who can use it. There is a basic backend interface for administrators to manage the metadata which describes these datasets.

## Installation
This installation guide was created for and tested on Ubuntu operating systems.

### 1. Packages/Dependencies
Install php environment
```
sudo apt-get install git curl zip unzip apache2 php-cli php-apcu php-pear php-dev php-dom php-curl libapache2-mod-php php-zip
pecl install apcu_bc
```

### 2. Digital Commons
Clone this repository into your server
```
git clone https://github.com/personalized-health-informatics-lab/digital-commons.git
```
Set up the Symfony configuration files. Make sure you have filled in the database and solr server (solrsearchr_server)
```
cd digital_commons
vim app/config/parameters.yml.example
cp app/config/parameters.yml.example app/config/dev/parameters.yml
```
Review security settings
```
vim app/config/security.yml.example
cp app/config/security.yml.example app/config/common/security.yml
```

### 3. Composer
Please check [Download Composer](https://getcomposer.org/download/) for details
```
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('SHA384', 'composer-setup.php') === '544e09ee996cdf60ece3804abc52599c22b1f40f4323403c44d44fdfdd586475ca9813a858088ffbc1f233e9b180f061') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"
sudo mv composer /usr/local/bin/composer
```
Install Symfony and other dependencies
```
composer install
```

### 4. Solr
We recommend using [Solr version 6](http://archive.apache.org/dist/lucene/solr/6.6.6/); version 7 or 8 should also work but we have not tested this
```
sudo apt-get install openjdk-8-jre
wget http://apache.cs.utah.edu/lucene/solr/6.6.6/solr-6.6.6.tgz
```
Start Solr instance
```
cd solr
bin/solr start
```
Create a new core for your project. Then place `SolrV6SchemaExample.xml` in the Solr config directory, name it schema.xml.

### 5. Database
Install the database packages
```
sudo apt-get install mysql-server php-mysql
```
Secure your installation
```
sudo mysql_secure_installation
```
Login to MySQL
```
mysql -u root -p

# Type the MySQL root password
```
Grant the user necessary permissions on the database
```
mysql> CREATE DATABASE IF NOT EXISTS dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
mysql> GRANT ALL PRIVILEGES ON `dbname`.* TO 'username'@'localhost';
mysql> FLUSH PRIVILEGES;
```
Quit the database session
```
mysql> \q
```
Initialize the database schema
```
php app/console doctrine:schema:update --force
```

### Web Server
Please check [Configuring a Web Server](https://symfony.com/doc/current/setup/web_server_configuration.html) for details.
The easiest way is to install the Apache recipe by executing the following command:
```
composer require symfony/apache-pack
```
The minimum configuration to get your application running under Apache is:
```
<VirtualHost *:80>
    ServerName domain.tld
    ServerAlias www.domain.tld

    DocumentRoot /var/www/project/public
    <Directory /var/www/project/public>
        AllowOverride All
        Order Allow,Deny
        Allow from All
    </Directory>

    # uncomment the following lines if you install assets as symlinks
    # or run into problems when compiling LESS/Sass/CoffeeScript assets
    # <Directory /var/www/project>
    #     Options FollowSymlinks
    # </Directory>

    ErrorLog /var/log/apache2/project_error.log
    CustomLog /var/log/apache2/project_access.log combined
</VirtualHost>
```
Enable rewrite mod and restart the server:
```
a2enmod rewrite
sudo service apache2 restart
```

## Follow-up Tasks
1. Once you've added some test datasets, you'll have to index them in Solr. You can edit and use `SolrIndexerExample.py` as an template and setup cron job to do indexing automatically.
2. You'll most likely want to brand the site with your institution's logo or color scheme. Some placeholders have been left in `app/Resources/views/base.html.twig` that should get you started.
3. In production, the site is configured to use the APC cache, which requires the installation of the APCu PHP module. Use the following command to refresh the cache:
```
php app/console cache:clear --env=prod
php app/console cache:warmup --env=prod
```
4. You'll most likely want to have some datasets to search. Get to it!!

## Using the API
The Data Catalog provides an API which can create and retrieve entities in JSON format.

### Listing Entities
Existing datasets and related entities can be retrieved by calling the appropriate endpoints. Each type of entity has a URL which matches its class name. You can use the filenames in the `src/AppBundle/Entity` directory as a reference since they also match the class names. For example, the Dataset entity is defined in `Dataset.php`, so a list of datasets in your catalog can be found at `/api/Dataset/all.json`. Subject Keywords are defined in `SubjectKeyword.php`, so a list of all your subject keywords can be found at `/api/SubjectKeyword/all.json`.
NOTE: The "all.json" is optional here, so `/api/Dataset` or `/api/SubjectKeyword` would work as well.

A *specific* dataset (or other entity) can be retrieved using its "slug" property (which you'd need to know beforehand). So, the URL `/api/Dataset/ama-physician-masterfile` will return the JSON representation of the AMA Physician Masterfile dataset.

In addition, the Dataset endpoint has an optional `output_format` parameter, which allows you to choose from three different output formats depending on your use case (all are returned as JSON):
- `default` - the default output format can be ingested directly by other data catalogs using this codebase
- `solr` - this format is suitable for indexing by Solr, and is used by our SolrIndexer scripts
- `complete` - this format returns a more complete representation of the dataset, including full information about its related entities
So for example, to retrieve the complete represenation of all your datasets, visit the URL `/api/Dataset/all.json?output_format=complete`

### Ingesting Entities
New entities can also be ingested using the API, but there are some extra steps:
1. __Grant API Privileges__ - Each user wishing to upload via the API must be granted this privilege in the user management area (at `/update/User`). Choose your user in the list and then check the "API User" role. When you save your changes, a unique API key will be generated, which will be used to verify the user's identity. The new key will be displayed the next time you view this form. The key is generated using Symfony's [random_bytes() function](https://symfony.com/doc/2.8/components/security/secure_tools.html#generating-a-secure-random-string) which is cryptographically secure. Please do not generate your own keys (except for testing) and PLEASE enforce HTTPS for all POST requests to the API, as this will keep your unique API key encrypted.
2. __Set X-AUTH-TOKEN Header__ - All POST requests to the API must include the user's API key as the X-AUTH-TOKEN header. Requests with missing API keys, or API keys corresponding to users who no longer have "API User" permissions will be rejected.
3. __Format your JSON__ - The entities you wish to ingest should be formatted in JSON in a way that Symfony can understand. We have provided a file in the base directory of this project called `JSON_sample.json`. This is a sample Dataset entity showing all the fields that are accepted by the API, and the types of values accepted by those fields. Note that many of the related entities fields (e.g. Subject Keywords) must already exist in the database before they can be applied to a new dataset via the API. For example, if you want to apply the keyword "Adolescent Health" to a dataset, you have to add "Adolescent Health" as a keyword before trying to ingest the dataset. There is more information about this in the `APITester.php` script. In this file you will see a sample PHP array which, like the sample JSON, shows the format required by the API (in case you're starting with your data in PHP). It also contains comments which go into a little more detail which fields require what.
4. __Perform the POST Request__ - The `APITester.php` script is a simple example of how to put together a POST request suitable for our API. Fill in the base URL of your data catalog installation (line 6), set the `$data` variable to contain the data you wish to ingest, and set the X-AUTH-TOKEN header to your API key (line 146). Please again note that most related entities can only be applied to new datasets if their values already exist in the database!

Luckily, these other entities can also be ingested via the API. Just like how we got a list of Subject Keywords by going to `/api/SubjectKeyword`, we can add new keywords by performing a POST request to `/api/SubjectKeyword`.

The API uses Symfony's form system to validate all incoming data, so the field names in your JSON should match the field names specified in each entity's form class. These files are located in `src/AppBundle/Form/Type`. Any fields that are required in the form class (or by database constraints) must be present in your JSON.

For example, if we check `src/AppBundle/Form/Type/SubjectKeywordType.php`, we can see which fields are required and what they should be called. Two fields are defined in this file, named "keyword" and "mesh_code". The MeSH code is set to `'required'=>false`. So, a new Subject Keyword can be added by submitting a POST request to `api/SubjectKeyword` with the body:
```
{
  "keyword": "Test keyword"
}
```
If we want to add the MeSH code as well, the request body would look like:
```
{
  "keyword": "Test keyword",
  "mesh_code": "the mesh code"
}
```

## License
[GPL](https://www.gnu.org/licenses/gpl-3.0.en.html)