function toggle_tree(lvl, i)
{
    el  = document.getElementById("dir_"+lvl);
    btn = document.getElementById("dirtoggle_"+lvl+"_"+i);
    
    if(el.style.display == "none") {
        el.style.display = "";
        btn.innerHTML = "-";
    }
    else {
       el.style.display = "none";
       btn.innerHTML = "+";
    }
}