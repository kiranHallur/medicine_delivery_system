console.log("radio js works");
let option_data_table = document.getElementById('option-data-table');
let option_data_table_tbody = document.querySelector('#option-data-table tbody');

class Radio{
    constructor(){
        console.log("cons");
    }

    appendRow(element){
        let index = this.getTblRows(), append = "option_data";
        let content = `        
            <td>
                <input value="" id="${append}[${index}][value]" name="${append}[${index}][value]" class="form-control" type="text" >
            </td>
            <td> 
                <input value="" id="${append}[${index}][title]" name="${append}[${index}][title]" class="form-control" type="text" >
            </td>
            <td> 
                <input value="${index}" id="${append}[0][default]" name="default_option_widget_id" class="form-control" type="radio">
            </td>
            <td>
                <button type="button" onclick="option_data.removeRow(this)" class="btn btn-danger btn-sm" >Remove</button>
            </td>
        `;
        let tr = document.createElement('TR');
        tr.innerHTML = content;
        option_data_table_tbody.appendChild(tr);
    }

    removeRow(element){
        element.parentElement.parentElement.remove();
    }

    getTblRows(){
        return option_data_table_tbody.childElementCount;
    }
}

option_data = new Radio();