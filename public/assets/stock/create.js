let validate_item_form = {
    "error_class" : "required",
    "elements" : {
        "#product_id" : 'required',
        "#stock_qty" : ['required', 'integer'],
        "#initial_qty" : ['required', 'integer'],
        "#price_per" : ['required', 'integer'],
    },
    "error_messages" : {
        "#product_id.required" : 'Choose any one product',
    }
};

let index_element = document.getElementById('index_field'); 
let stock_item_element = document.getElementById('stock_item_id'); 
let product_element = document.getElementById('product_id');
let barcode_element = document.getElementById('barcode');
let price_per_element = document.getElementById('price_per');
let initial_qty_element = document.getElementById('initial_qty');
let stock_qty_element = document.getElementById('stock_qty');
let is_closed_element = document.getElementById('is_closed');
let items_table_element = document.getElementById('items-table');
let items_table_tbody_element = items_table_element.querySelector('tbody');


function push_item() {
    let result = run_validation(validate_item_form);
    // console.log(result);
    if(result["is_valid"]==true){
        let obj = get_item_form_data();
        if(index_element.value==""){
            items_container['items'].push(new Item(obj));
            empty_item_form_data();
            table_render();
        }

        if(index_element.value!="" && stock_item_element.value==""){
            items_container['items'][index_element.value]= new Item(obj);
            empty_item_form_data();
            table_render();
        }

        if(index_element.value!="" && stock_item_element.value!=""){
            let form_data = new FormData();
            form_data.append('_token', tokenId);
            form_data.append('pk', stock_item_element.value);
            form_data.append('product_id', obj['product_id']);
            form_data.append('barcode', obj['barcode']);
            form_data.append('price_per', obj['price_per']);
            form_data.append('initial_qty', obj['initial_qty']);
            form_data.append('stock_qty', obj['stock_qty']);
            form_data.append('is_closed', obj['is_closed']);
            let res = postFetch(`${api_url}stock/item/update`, form_data);
            toastr.info("Updating item...");
            res.then((data) => {
                toastr.clear();
                if(data['success']){
                    items_container['items'][index_element.value] = new Item(obj);
                    toastr.success(data['msg']);
                    empty_item_form_data();
                    table_render();
                }else{
                    toastr.error(data['msg']);
                }
            });
        }
    }else{
        toastr.info('Please fill * marked fields.');
    }
} 

function remove_item(index, stock_item_id=null){
    
    let cfn = confirm("Confirm to remove");
    if(!cfn){
        return false;
    }

    if(stock_item_id==null){
        items_container['items'].splice(index,1);
        table_render();
    }else{
        toastr.info("Removing item...");
        let form_data = new FormData();
        form_data.append('_token', tokenId);
        form_data.append('pk', stock_item_id);
        let res = postFetch(`${api_url}stock/item/remove`, form_data);
        res.then((data) => {
            toastr.clear();
            if(data['success']){
                items_container['items'].splice(index,1);
                toastr.success(data['msg']);
                table_render();

            }else{
                toastr.error(data['msg']);
            }
        });
    }   
}



function edit_item(index, stock_item_id=null){
    let obj = items_container['items'][index];

    index_element.value = index;
    stock_item_element.value = stock_item_id;
    product_element.value = obj['product_id'];
    barcode_element.value = (obj['barcode']!=null && obj['barcode']!="null")? obj['barcode'] : '';
    price_per_element.value = (obj['price_per']!=null && obj['price_per']!="null")? obj['price_per'] : '';
    initial_qty_element.value = obj['initial_qty'];
    stock_qty_element.value = obj['stock_qty'];
    is_closed_element.checked = (obj['is_closed']==0)? false : true;
}

function empty_item_form_data(){
        index_element.value="",
        stock_item_element.value="",
        product_element.value=0
        barcode_element.value = "";
        price_per_element.value = "";
        initial_qty_element.value = "";
        stock_qty_element.value = "",
        is_closed_element.checked=false
}

function get_item_form_data(){
    let is_closed_checked = (is_closed_element.checked==true)? 1 : 0;

    return {
        "stock_item_id" : stock_item_element.value || null,
        "product_id" : product_element.value || null,
        "name" : product_element.selectedOptions[0].innerText,
        "barcode" : barcode_element.value || null,
        "price_per" : price_per_element.value || 0,
        "initial_qty" : initial_qty_element.value || 0,
        "stock_qty" : stock_qty_element.value || 0,
        "is_closed" : is_closed_checked,
    }
}

let items_container = {
    "items" : [],
}

class Item{
    constructor(data){
        Object.assign(this,data)
        let price_per = (!isNaN(this['price_per']))? parseToFloat(this['price_per']) : 0;
        this['price_per'] = price_per;
    }
}

function table_render(){
    let context = ``;
    items_container['items'].forEach((item, index)=>{
        context += `
            <tr>
                <td>
                    ${item['name'] || 'NA'}    
                </td>
                <td>${string_cast(item['barcode'],'NA')}</td>
                <td>${item['price_per'] || 'NA'}</td>
                <td>${item['initial_qty'] || 'NA'}</td>
                <td>${item['stock_qty'] || 'NA'}</td>
                <td>${(item['is_closed']==0)? "No" : "Yes"}</td>
                <td>
                ${
                    (current_user_role_id!=app_constants['ADMIN_ROLE_ID'])? 
                        `<button type="button" onclick="edit_item(${index}, ${item['stock_item_id'] || null})" class="btn btn-primary btn-md">Edit</button>
                        <button type="button" onclick="remove_item(${index}, ${item['stock_item_id'] || null})" class="btn btn-danger btn-md">Remove</button>` : ''
                }
                    
        ${
            (item['stock_item_id'] == undefined || item['stock_item_id'] == null)?
                `<input type="hidden" name="items[${index}][product_id]" value="${item['product_id']}">
                <input type="hidden" name="items[${index}][barcode]" value="${item['barcode']}">
                <input type="hidden" name="items[${index}][price_per]" value="${item['price_per']}">
                <input type="hidden" name="items[${index}][initial_qty]" value="${item['initial_qty']}">
                <input type="hidden" name="items[${index}][stock_qty]" value="${item['stock_qty']}">
                <input type="hidden" name="items[${index}][is_closed]" value="${item['is_closed']}">`
            : ''
        }

                </td>
            </tr>
        `;
    });

    items_table_tbody_element.innerHTML = context;
}