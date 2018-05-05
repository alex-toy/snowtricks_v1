alert('lazyloading')


var showNumber = 0;
    var step = 5;
    var figures = document.getElementsByClassName("figure_min");
    
        var numberFigures = 2;
        console.log(numberFigures);
    
    
    
    window.addEventListener('scroll', function ( event ) {
        
        var total = document.body.scrollTop + window.innerHeight;
        
        if(total >= document.body.scrollHeight && showNumber < numberFigures ){
            showNumber += step;
            if( showNumber > numberFigures ){ showNumber = figures.length }
            
            for (var i = showNumber-step; i < showNumber; i++) {
                figures[i].style.display = "block";
            }
            
            
            for (var opacity = 0; opacity < 1; opacity += 0.00001) {
                for (var i = showNumber-step; i < showNumber; i++) {
                    figures[i].style.opacity = opacity;
                }
                
            }
            
        }
        
    }, false);


