## Cobiro's Task Menu

This project is a technical test Cobiro sent me in order to create a Restful API using PHP.
For this project, I decided to work with Laravel because I like to work with artisan when I have to create an API.

The goal of this API was to be abble to create menus which are composed of items or submenus (such as in the composite design pattern) creating different layers in this menu.
Also it has to give the ability to create multiple menus, and to retrieve a specific menu, a specific layer or a specific item in the menu.

One of the requests was to decouple as much as possible the different controllers and elements in the application.

The API provides a list of routes to handle the different behaviours we want to have using different HTTP verbs. They are all listed in the route/api.php file, but some example of the routes provided are :

POST : /menus --> Create a new menu<br/>
GET : /menus/{menu} --> Get an existing menu<br/>
POST : /menus/{menu}/items --> Store a new item in a specific menu<br/>
POST : /items --> Store an item with no menu<br/>
GET : /menus/{menu}/depth --> Getting the depth of a menu (The number of layers)<br/>

I sent this project as a submission to Cobiro's exercise.
I had some good feedback about my technical test and my interview, but they were looking for a Senior profile so I didn't went further in the process.
I decided to keep this project in my repositories, because I think it was a pretty complex project but small enough to be reviewed quickly.

I hope it will interests you, if you have any questions, do not hesitate to contact me. (karimmorel.com)
