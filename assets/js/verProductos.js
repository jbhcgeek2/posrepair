let buscarP = document.getElementById('buscarProducto');

buscarP.addEventListener('change',function(){
    // alert('aa');
});


function buscarProd(){
    let cat = document.getElementById('catBus').value;
    let buscar = document.getElementById('buscarProducto').value;

    // console.log(buscar);
    let datos = new FormData();
    datos.append('busProds',buscar);
    datos.append('catBus',cat);
    datos.append('sendBusqueda','yes');


    let envio = new XMLHttpRequest();
    envio.open('POST','../includes/verProducto.php',false);
    envio.send(datos); 
    // alert('pasa rey');
    // document.getElementById('auxRes').innerHTML = 'nada';


    if(envio.status == '200'){
        let res = JSON.parse(envio.responseText);
        let contenido = "";
        console.log(res);
        
        if(res.status == "ok"){
            //verificamos si cuenta con resultados
            if(res.data != "NoData"){

            }else{
                //sin resultados
                contenido = `<tr>
                <td colspan='5' style='text-align:center;'>Sin resultados</td>
                </tr>`;
            }
            document.getElementById('resProdBus').innerHTML = contenido;
        }else{
            Swal.fire(
                'Ha ocurrido un error',
                res.mensaje,
                'error'
            )
            
        }
        // alert(envio.responseText);
    }else{
        Swal.fire(
            'Servidor Inalcansable',
            'Verifica tu conexion a internet',
            'error'
        )
    }
}