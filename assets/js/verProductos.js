
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
                for (let x = 0; x < res.data.length; x++) {
                    let nombreProd = res.data[x].nombreArticulo;
                    let precio = res.data[x].precioUnitario;
                    let idProd = res.data[x].idArticulo;
                    let prove = res.data[x].nombreProveedor;
                    let existen = res.data[x].existencia;

                    contenido = contenido+`
                    <tr>
                        <td>${nombreProd}</td>
                        <td>${prove}</td>
                        <td>$${precio}</td>
                        <td style='text-align:center;'>${existen}</td>
                        <td class='text-center'>
                            <a class='btn btn-success' href='verInfoProducto.php?infoProd=${idProd}'>Ver</a>
                        </td>
                    </tr>`; 
                }//fin del for

            document.getElementById('resProdBus').innerHTML = contenido;
            document.getElementById('codigoProd').value = '';
            }else{
                //sin resultados
                contenido = `<tr>
                <td colspan='5' style='text-align:center;'><h5>Sin resultados</h5></td>
                </tr>`;
            }
            document.getElementById('resProdBus').innerHTML = contenido;
            document.getElementById('codigoProd').value = '';
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

function buscarCodigo(){
    
    let codigo = document.getElementById('codigoProd').value;

    // console.log(buscar);
    let datos = new FormData();
    datos.append('buscarCodigo',codigo);


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
                for (let x = 0; x < res.data.length; x++) {
                    let nombreProd = res.data[x].nombreArticulo;
                    let precio = res.data[x].precioUnitario;
                    let idProd = res.data[x].idArticulo;
                    let prove = res.data[x].nombreProveedor;
                    let existen = res.data[x].existencia;

                    contenido = contenido+`
                    <tr>
                        <td>${nombreProd}</td>
                        <td>${prove}</td>
                        <td>$${precio}</td>
                        <td style='text-align:center;'>${existen}</td>
                        <td class='text-center'>
                            <a class='btn btn-success' href='verInfoProducto.php?infoProd=${idProd}'>Ver</a>
                        </td>
                    </tr>`;
                }//fin del for

            document.getElementById('resProdBus').innerHTML = contenido;
            }else{
                //sin resultados
                contenido = `<tr>
                <td colspan='5' style='text-align:center;'><h5>Sin resultados</h5></td>
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