# mvc
Simple MVC Framework for PHP

##Use
When creating a new controller, the name of the controller will be the route/url. This framework will also make sure that the correct number of parameters are passed to the functions inside your controller. 

On top of that, type-hinting will also be taken into account. By using int, float or double, string, array or object, the data that is sent from the user will automatically be casted to that type.

If you use array or object as type-hints, you will be able to receive json data that will automatically be casted to either an object or an array.

If any exception that is related to the core funtionallity is thrown, the controller named Error will automatically be called and you will see the error message together with the stack trace. You can change this information by changing the view/error/index.

##Alpha
This project is in alpha and a more detailed description will be added soon.
