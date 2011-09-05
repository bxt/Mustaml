Mustaml Demo Site Using PHP & Javascript
========================================

This is a small demo using Mustaml on the server (PHP) and on the client (JS). It is a very basic to-do list application where you can add and check items via "AJAX", if JS is available and via traditional form submission as a fallback. 

Running this demo
-----------------

To get this site running, symlink the `demosite` directory to your local Apache HTTP root directory (e.g. `cd /srv/www/htdocs && ln -s /home/yourname/mustaml/demos/demosite/`) and navigate to <http://localhost/demosite>. If your web server does not follow symlinks you can copy the whole `demosite` directory and then update the symbolic link to `mustaml.min.js`. 

Files included
--------------

It is made of a simple PHP controller (`index.php`) which handles non-static requests. On AJAX requests it renders just a short plain text message, on full-page requests it will render the to-do list using Mustaml. The `Todos.php` file contains everything to persist the to-do list in the text file `todos.txt`. 

The Mustaml templates are located in the directory `tmpl`. There is one named `index.mustaml`, which contains the basic HTML structure and the form for adding items. The other template, `todo.mustaml` is used for rendering a to-do item and is exported to Javascript too. 

The Javascript part consists of a symbolic link to `mustaml.min.js` and the main `script.js`, which enables AJAX for the forms and renders the Mustaml templates when necessary. Note that the template file is loaded via XHR too. Additionally jQuery from Google CDN is used to simplify DOM manipulation and XHR, it is however not required to use Mustaml. 

Lastly `style.css` contains some CSS rules to make the to-do list a little more user friendly. 
