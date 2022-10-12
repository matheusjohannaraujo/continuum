
window.addEventListener("load", function(event) {
    show_info_framework = document.querySelector("#show_info_framework")    
    setTimeout(function(){
        console.clear()
        if (!show_info_framework || window.sessionStorage.getItem("show_info_framework") !== null) {
            return
        }
        show_info_framework.click()
        window.sessionStorage.setItem("show_info_framework", true)
        /*var i = 0
        var scale = 1
        var marginLeft = 0
        var borderRadius = 3
        var direction = false*/
        var i = 360
        var scale = 0.10000000000000145
        var marginLeft = 360
        var borderRadius = 46.20000000000007
        var direction = !false
        var interval = setInterval(() => {        
            if (!direction) {
                i += 5
                scale -= 0.0125
                marginLeft += 5
                borderRadius += 0.6
            } else {
                i -= 10
                scale += 0.025
                marginLeft -= 10
                borderRadius -= 1.2
            }
            if (i <= 0) {
                clearInterval(interval)
                i = 0
                scale = 1
                marginLeft = 0
                borderRadius = 3
            }            
            if (i >= 360) {
                direction = true
            }
            autor.style.transform = `rotate(${i}deg) scale(${scale})`
            autor.style.marginLeft = `${marginLeft}px`
            autor.style.borderRadius = `${borderRadius}px`
        }, 20)
    }, 2500)
})
