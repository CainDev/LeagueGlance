function setBackground(){
    var totalBackgrounds = 30;
    var randomBackground = Math.ceil(Math.random() * totalBackgrounds)
    document.body.background = "./assets/backgrounds/" + randomBackground + ".jpg";
}

function setSearchQuote(){
    var searchButton = document.getElementById("summoner-submit");
    switch(Math.ceil(Math.random() * 10)){
        case 1:
            return searchButton.innerHTML = "Let's start the show".toUpperCase();
        case 2:
            return searchButton.innerHTML = "The dance begins".toUpperCase();
        case 3:
            return searchButton.innerHTML = "Tonight, we hunt!".toUpperCase();
        case 4:
            return searchButton.innerHTML = "They will not expect this!".toUpperCase();
        case 5:
            return searchButton.innerHTML = "I'll scout ahead!".toUpperCase();
        case 6:
            return searchButton.innerHTML = "I know the true path".toUpperCase();
        case 7:
            return searchButton.innerHTML = "Set sail!".toUpperCase();
        case 8:
            return searchButton.innerHTML = "Let me get in there!".toUpperCase();
        case 9:
            return searchButton.innerHTML = "Where's the action?".toUpperCase();  
        case 10:
            return searchButton.innerHTML = "Let's get this party started!".toUpperCase();          
        default:
            return searchButton.innerHTML = "Tonight, we hunt!".toUpperCase();
    }
}