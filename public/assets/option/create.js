let template_div = document.getElementById('template');
let prev_selected_widget = "";
function showRepectiveForm(element, option_id=null){
    let widget_id = null;

    try {
        widget_id = element.selectedOptions[0].value;
    } catch (error) {
        widget_id = null;
    }

    if(widget_id){
        let script_id = `script_${prev_selected_widget}`;
        let script_elements = document.querySelectorAll(`script[data-id="${script_id}"]`);
        prev_selected_widget = widget_id;
        
        if(script_elements.length > 0){
            for(let script in script_elements){
                // script_elements[script].src="";
            }
        }

        toastr.info("Loading Option Data ....");
        let html="";
        let url = base_url(`backend/option/get-widget-template/${widget_id}?option_id=${option_id}`);
        let result = getFetch(url);
        result.then((data) => {
            let js = data['js'] || [];
            let nodes = [];
            if(js.length > 0){
                for(let node in js){
                    if(scripts = document.querySelectorAll(`script[src='${js[node]}']`).length == 0){
                        let script_element = document.createElement('SCRIPT');
                        script_element.setAttribute('data-id', `script_${data['id']}`);
                        script_element.setAttribute('src', js[node]);                    
                        nodes.push(script_element);
                    }else{

                    }
                }
                pushNodes(document.getElementsByTagName('body')[0], nodes);
                // console.log(nodes);
            }

            html = data["template"];
            template_div.innerHTML = html;
            toastr.success("Option Loaded."); 
        });
    }

    // render to template
}
