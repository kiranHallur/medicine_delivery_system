let attribute_table = document.getElementById('attribute-table');
let slider_images_table = document.getElementById('slider-images-table');

function getAttributeGroups(){
    let content = `<option value="">Choose</option>`;
    for(let i in attribute_groups){
        content += `<optgroup label="${attribute_groups[i]["name"]}">`;
        for(let j in attribute_groups[i]['attributes']){
            let attribute = attribute_groups[i]['attributes'][j];
            content += `<option value="${attribute["attribute_id"]}">${attribute["name"]}</option>`;
        }
        content += `</optgroup>`;
    }

    return content;

}

function attributeAppendRow(ele){
    let tbody = attribute_table.querySelector('tbody');
    let attribute_index = tbody.childElementCount || 0;
    let append = `attributes[${attribute_index}]`;
    let content = `
    
        <td>
            <select name="${append}[attribute_id]" id="${append}[attribute_id]" class="form-control" >
                ${getAttributeGroups()}
            </select>
        </td>
        <td>
            <textarea class="form-control" type="text" name="${append}[attribute_text]" id="${append}[attribute_text]" ></textarea>
        </td>

        <td>
            <input class="form-control" type="number" value="" name="${append}[sort_order]" id="${append}[sort_order]" >
        </td>

        <td> 
            <button type="button" onclick="attributeRemoveRow(this)" class="btn btn-danger btn-sm waves-effect waves-light">
                <i class="mdi mdi-close-circle"></i>
            </button>
        </td>`;
    let element = document.createElement('TR');
    element.innerHTML = content;
    tbody.appendChild(element);
}

function attributeRemoveRow(ele){
    ele.parentElement.parentElement.remove();
}

function sliderImageAppendRow(ele){
    let tbody = slider_images_table.querySelector('tbody');
    let slider_images_index = tbody.childElementCount || 0;
    let append = `slider_images[${slider_images_index}]`;
    let content = `
    
        <td>
            <input type="file" name="${append}[path]" id="${append}[path]" >
        </td>
        <td>
            <input class="form-control" type="number" name="${append}[sort_order]" id="${append}[sort_order]" value="" >
        </td>
        <td> 
            <button type="button" onclick="sliderImageRemoveRow(this)" class="btn btn-danger btn-sm waves-effect waves-light">
                <i class="mdi mdi-close-circle"></i>
            </button>
        </td>`;
    let element = document.createElement('TR');
    element.innerHTML = content;
    tbody.appendChild(element);
}

function sliderImageRemoveRow(ele, id=null){
    if(!id){
        ele.parentElement.parentElement.remove();
    }else{
        let cnf = confirm('Confirm to remove ?');
        if(cnf==true){
            var formData = new FormData();
            formData.append('id', id);
            formData.append('_token', tokenId);
            extendToastrTimeOut();
            toastr.info('Removing...');

            let result = postFetch(`${api_url}backend/product/slider-image/remove/${id}`, formData);

            result.then(function (data) {
                clearToastr();
                if (data["success"]) {
                    toastr.success(data["msg"]);
                    ele.parentElement.parentElement.remove();
                } else {
                    toastr.error(data["msg"]);
                }
            }).catch(function (err) {
                console.log('Error', err);
            });
        }
    }
}

function removeImage(ele, id){
    let cnf = confirm('Confirm to remove ?');
    if(cnf==true){
        var formData = new FormData();
        formData.append('product', id);
        formData.append('_token', tokenId);
        extendToastrTimeOut();
        toastr.info('Removing...');

        let result = postFetch(`${api_url}backend/product/image/remove/${id}`, formData);

        result.then(function (data) {
            clearToastr();
            if (data["success"]) {
                toastr.success(data["msg"]);
                ele.parentElement.parentElement.remove();
            } else {
                toastr.error(data["msg"]);
            }
        }).catch(function (err) {
            console.log('Error', err);
        });
    }
}

function showOptionsForGroup(element){
    let id= element.selectedOptions[0].value || null;
    if(id){
        extendToastrTimeOut();
        toastr.info('Fetching Options for Group...');
    
        let result = getFetch(`${api_url}backend/product/option-group/${id}`);
    
        result.then(function (data) {
            clearToastr();
            if (data["options"].length>0) {
                toastr.success("Options loaded.");
                loadOptionsForGroup(data['options']);
            } else {
                toastr.error("No options for this group.");
            }
        }).catch(function (err) {
            console.log('Error', err);
        });
    }    
}

// Options
let options_template = document.getElementById('options_template');

function loadOptionsForGroup(options){
    let content = `<div id="accordion">`;
    for(let index in options){
        content += optionAccordion(options[index], index);
    }
    content += `</div>`;

    options_template.innerHTML = content;

}

function optionAccordion(option, index){
    let append = `options[${index}]`;
    let content = `
        <div class="card mb-0">
            <div class="card-header p-3" id="option_${index}">
                <a href="#collapse_option_${index}" class="text-dark" data-toggle="collapse" aria-expanded="true" aria-controls="collapse_option_${index}">
                    <h6 class="m-0">${option["option_name"]}</h6>
                </a>
            </div>
            <div id="collapse_option_${index}" class="collapse show" aria-labelledby="option_${index}" data-parent="#accordion">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label for="" class="col-sm-12 col-form-label">Validation</label>
                                <div class="col-sm-10">
                                    <input type="hidden" name="${append}[option_id]" id="${append}[option_id]" value="${option['option_id']}" >
                                    <input value="${option["validation_arr"] || "" }" id="" class="form-control" type="text"  disabled>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="" class="col-sm-12 col-form-label">Option Data</label>
                                <div class="col-sm-12">
                                    <table class="table table-bordered mb-0">
                                        <thead>
                                            <tr>
                                                <th>Id</th>
                                                <th>Title</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>`;
                                        for(let i in option["option_widgets"]){
                                            content += loadOptionTableRow(option["option_widgets"][i], index, i);
                                        }
                            content += `</tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <h4 class="mt-0 header-title">Customize Option</h4>
                            <div class="form-group row">
                                <label for="" class="col-sm-12 col-form-label">Display of Option Widget</label>
                                <div class="col-sm-10">
                                    <select id="${append}[display_option_widget]" name="${append}[display_option_widget]" class="form-control">
                                        <option value="">Choose</option>
                                        <option value="SHOW_ONLY_TEXT">Show Only Text</option>
                                        <option value="SHOW_ONLY_IMAGE">Show Only Image</option>
                                        <option value="SHOW_ONLY_TEXT_AND_IMAGE">Show Both (Text & Image)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="" class="col-sm-12 col-form-label">Option Status</label>
                                <div class="col-sm-10">
                                    <select id="${append}[status]" name="${append}[status]" class="form-control">
                                        <option value="1">Enabled</option>
                                        <option value="0">Disabled</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>                    
                </div>
            </div>
        </div>
    `;

    return content;
}

function loadOptionTableRow(option_widget, option_index=0, option_widget_index=0){
    let append = `options[${option_index}][option_widgets][${option_widget_index}]`;
    let content = `
        <tr>
            <td>${option_widget["value"] || 'NA'}</td>
            <td>${option_widget["option_widget_title"] || 'NA'}</td>
            <td>
                <div>
                    <a href="javascript:void(0)" onclick="triggerFileField('${append}[image]')" class="btn btn-primary btn-xs">Upload Image</a>
                    <input type="file" name="${append}[image]" id="${append}[image]" class="disp_none" >
                    <input type="hidden" value="${option_widget['option_widget_id']}" name="${append}[option_widget_id]" id="${append}[option_widget_id]" >
                </div>
            </td>
        </tr>
    `;

    return content;
}

function triggerFileField(id){
    document.getElementById(id).click();
}