# OpenSkos-API
Restful API for OpenSkos

This is a new API for the OpenSkos project. The aim is to have a great speed/reaction advantage 
in this API compared to the original projects when making changes to the database. 


## Docker
Everything regarding the Docker setup/configuration can be found in the `/docker` directory. 

The folder `/docker/nginx` contains all configuration files for the NginX container.  

The folder `/docker/fpm` contains all configuration files for the Php-Fpm container. 

## Development installation:

    composer install -o
    docker-compose up -d
    
The envirioment should be up & running at http://localhost:9020. 
Open http://localhost:9020/ping to open the healthcheck page and see if the setup has gone correctly. The text 
`Hello OpenSkos world!` should be displayed. 


## GrumPHP, PHPCS fixer & PHPStan
This project uses these pacakges to have some enforce code styling and quality. Before committing your work
your should run `composer grumphp` to see if the code passes the checks. If not, it's possible to run 
`composer phpcsfixer` to try to automaticly fix the issues. Then try `composer grumphp` again. If errors stil occur,
manual corrections are needed. The error messages should contain the nessesery information to help to locate & fix the problem.
Once `composer grumphp` passes, you can commit your code to your feature branch. 

To run grump

    composer grumphp
    
To run PhpCs (just checking for errors)

    composer phpcs
    
To PhPCsFixer (checking & fixing errors when possible)

    composer phpcsfixer
