# mvc
Simple MVC Framework for PHP

##Use
When creating a new controller, the name of the controller will be the route/url. The framework will make sure that the correct number of parameters are passed to the function. 

This framwork will also take into account the type-hinting of the parameter. By using int, float or double, string, array or object, the data that is sent from the user will automatically be cased to that type.

If you use array or object as type-hints, you can send json data that will automatically be cased to either object or an array.

If any exception that is related to the core funtionallity is thrown, the controller named Error will automatically be called and you will see the error message together with the stack trace.

##Alpha
This project is in alpha and a more detailed description will be added soon.
