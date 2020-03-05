// ============ Remonter tout en haut de la page==========================================

const btn_ride_up=document.querySelectorAll(".btn_ride_up");
for ( let i=0;i<btn_ride_up.length;i++){
    btn_ride_up[i].addEventListener('click',ride_up);
    function ride_up(){
        window.scroll(0,0);
    }
}