function extendToastrTimeOut(time=0, position='toast-top-right') {
    toastr.options = {"timeOut": time};
}

function clearToastr(){
    toastr.clear();
}

function base_url(url) {
    var base_url = $("#base_url").val();
    var new_url = base_url + '/' + url;
    return new_url;
}

toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": true,
    "progressBar": true,
    "positionClass": "toast-top-right",
    "preventDuplicates": true,
    "onclick": null,
    "showDuration": 300,
    "hideDuration": 1000,
    "timeOut": 5000,
    "extendedTimeOut": 1000,
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
}

async function getFetch(endpoint = '', options={}) {
    let header_data = headers;
    if(options["headers"]){
        header_data = options["headers"];
    }
    
    return fetch(endpoint, {
        method: "GET", // *GET, POST, PUT, DELETE, etc.
        mode: 'cors', // no-cors, cors, *same-origin
        cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
        credentials: 'same-origin', // include, *same-origin, omit
        // headers: {
        //     'Content-Type': 'application/json',
        //     'format': 'json',
        // },
        headers : header_data,
        redirect: 'follow', // manual, *follow, error
        referrer: 'no-referrer', // no-referrer, *client
    })
    .then(response => response.json()); // parses JSON response into native Javascript objects 
}

async function postFetch(endpoint = '',data={},options={}) {
    let header_data = headers; 
    if(options["headers"]){
        header_data = options["headers"];
    }
    return fetch(endpoint, {
            method: "POST", // *GET, POST, PUT, DELETE, etc.
            credentials: "same-origin", // include, same-origin, *omit
            body: data,
            headers : header_data,
        }).then(response => response.json()); // parses JSON response into native Javascript objects 
}

// Elements 
let js_elements = {
    'tr' : document.createElement('TR')
};

function pushNodes(element, nodes=[]){
    if(nodes.length > 0){
        for(let node in nodes){
            element.appendChild(nodes[node]);
        }
    }
}

function run_validation(obj={},options={}){

    function is_required(element){
        if(element.ELEMENT_NODE && element.value!=null && element.value!=undefined && element.value!=""){
            return true;
        }else{
            return false;
        }
    }

    function process_integer(element){
        if(element.ELEMENT_NODE && isNaN(element.value)==false){
            return true;
        }else{
            return false;
        }
    }

    function append_error_message(choice, name, element,error_message){
        let label_element=document.createElement('LABEL');
        let stripped_name = name.substr(1);
        let label_id = `${stripped_name}-error-label-${choice}`;
        label_element.id=label_id;
        label_element.innerText=default_error_message;
        label_element.setAttribute('class', error_class);

        let current_label_element = document.getElementById(label_id) || null;
        if(current_label_element==null){
            if(element.parentElement){
                element.parentElement.appendChild(label_element);
            }
        }else{
            current_label_element.innerText = default_error_message;
        }

    }

    function remove_error_messages(choice, name){
        let stripped_name = name.substr(1);
        let label_id = `${stripped_name}-error-label-${choice}`;
        let current_label_element = document.getElementById(label_id) || null;
        if(current_label_element!=null){
            current_label_element.remove();
        }
    }

    function get_field_name(name){
        return name.substr(1);
    }

    function choice_selector(choice, name, element, error_messages_object){
        switch (choice) {
            case "required":
                // get_particular_error_message_from_object(choice, name, error_messages_object);
                res = is_required(element);
                default_error_message= error_messages_object[`${name}.${choice}`] || "This field must be filled" ;
                break;

            case "integer":
                res = process_integer(element);
                default_error_message= error_messages_object[`${name}.${choice}`] || "This field must be integer" ;
                break;
        
            default:
                break;
        }

        return {
            "success" : res,
            "error_message" : default_error_message,
        };
    }

    function get_particular_error_message_from_object(field, error_messages_object){
        
    }

    let value,choice,res,default_error_message="",error_messages=[], error_class=obj['error_class'] || '';
    let is_all_clean=true;
    if(typeof(obj)=="object"){
        let elements = obj['elements'] || []; 
        let is_type = "object";
        if(elements.length){
            is_type = "array";
        }
        for(let name in elements){
            // console.log(name);
            element = document.querySelector(name);
            choice = elements[name];
            if(typeof(choice)=="string"){
                res = choice_selector(choice, name, element, obj['error_messages'] || {});
            }else if(choice.length || null){
                for(let sub_choice in choice){
                    res = choice_selector(choice[sub_choice], name, element, obj['error_messages'] || {});

                    if(!res["success"]){
                        error_messages.push(res['error_message']);
                        // element.appendChild(label_element);
                        append_error_message(choice[sub_choice], name, element,default_error_message);
                    }else{
                        remove_error_messages(choice[sub_choice], name);
                    }
                }
            }

            if(!res["success"]){
                error_messages.push(res['error_message']);
                // element.appendChild(label_element);
                append_error_message(choice, name, element,default_error_message);
            }else{
                remove_error_messages(choice, name);
            }
        } 
    }

    function is_valid(){
        let is_clean=true;
        if(error_messages.length>0){
            is_clean=false;
        }
        return is_clean;
    }

    function get_error_messages(){

    }

    return {
        "is_valid" : is_valid(),
    }
}


function parseToFloat(value) {
    let type_of = typeof value;
    let context = 0;
//    if (type_of != "number") {
        context = parseFloat(Number(value).toFixed(2));
//    }
    return context;
}

function parseToString(value) {
    let type_of = typeof value;
    let context = value;
    if (type_of != "string") {
        context = context.toString();
    }
    return context;
}

function string_cast(value, fallback_value){
    let context = fallback_value;
    if(value != "null" && value != null && value != undefined && value != ""){
        context = value;
    }
    return context;
}