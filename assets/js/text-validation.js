// I also validate it server side. Don't waste your time lol.

function formValidation(){
    var form_text = document.forms["summoner-search"]['summoner-name'].value;
    var invalid_char_array = '"`¬$£%*()_-+={[}]@\'~#?/>.<,;:|\\'.split('');
    console.log(invalid_char_array);

    if(form_text.length < 3 || form_text.length > 16){
        alert("Please enter a name between 3 and 16 Characters.");
        return false;
    }
    
    for(let index = 0; index < invalid_char_array.length; index++){
        if(form_text.includes(invalid_char_array[index])){
            alert("Please remove any banned symbols from your name.");
            return false;
        }
    }
}