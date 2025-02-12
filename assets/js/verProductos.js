
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
                            <div class='tooltip-container'>
                            <span class='tooltip-text'>Traspasar</span>
                            <a href='#!' class='btn btn-warning' id='${idProd}' onclick='traspasaProd(this.id)'>
                                <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-repeat' viewBox='0 0 16 16'>
                                <path d='M11 5.466V4H5a4 4 0 0 0-3.584 5.777.5.5 0 1 1-.896.446A5 5 0 0 1 5 3h6V1.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384l-2.36 1.966a.25.25 0 0 1-.41-.192m3.81.086a.5.5 0 0 1 .67.225A5 5 0 0 1 11 13H5v1.466a.25.25 0 0 1-.41.192l-2.36-1.966a.25.25 0 0 1 0-.384l2.36-1.966a.25.25 0 0 1 .41.192V12h6a4 4 0 0 0 3.585-5.777.5.5 0 0 1 .225-.67Z'/>
                                </svg>
                            </a>
                            </div>
                            <div class='tooltip-container'>
                            <span class='tooltip-text'>Ingresar</span>
                            <a href='#!' class='btn btn-info' id='${idProd}' onclick='ingresoProd(this.id)'>
                                <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-box-arrow-in-right' viewBox='0 0 16 16'>
                                <path fill-rule='evenodd' d='M6 3.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 0-1 0v2A1.5 1.5 0 0 0 6.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-8A1.5 1.5 0 0 0 5 3.5v2a.5.5 0 0 0 1 0z'/>
                                <path fill-rule='evenodd' d='M11.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H1.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z'/>
                                </svg>
                            </a>
                            </div>
                            <div class='tooltip-container'>
                            <span class='tooltip-text'>Salida</span>
                            <a href='#!' class='btn btn-danger' id='${idProd}' onclick='salidaProd(this.id)'>
                                <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-box-arrow-up' viewBox='0 0 16 16'>
                                <path fill-rule='evenodd' d='M3.5 6a.5.5 0 0 0-.5.5v8a.5.5 0 0 0 .5.5h9a.5.5 0 0 0 .5-.5v-8a.5.5 0 0 0-.5-.5h-2a.5.5 0 0 1 0-1h2A1.5 1.5 0 0 1 14 6.5v8a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 14.5v-8A1.5 1.5 0 0 1 3.5 5h2a.5.5 0 0 1 0 1z'/>
                                <path fill-rule='evenodd' d='M7.646.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 1.707V10.5a.5.5 0 0 1-1 0V1.707L5.354 3.854a.5.5 0 1 1-.708-.708z'/>
                                </svg>
                            </a>
                            </div> - 
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
                        <div class='tooltip-container'>
                            <span class='tooltip-text'>Traspasar</span>
                            <a href='#!' class='btn btn-warning' id='${idProd}' onclick='traspasaProd(this.id)'>
                                <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-repeat' viewBox='0 0 16 16'>
                                <path d='M11 5.466V4H5a4 4 0 0 0-3.584 5.777.5.5 0 1 1-.896.446A5 5 0 0 1 5 3h6V1.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384l-2.36 1.966a.25.25 0 0 1-.41-.192m3.81.086a.5.5 0 0 1 .67.225A5 5 0 0 1 11 13H5v1.466a.25.25 0 0 1-.41.192l-2.36-1.966a.25.25 0 0 1 0-.384l2.36-1.966a.25.25 0 0 1 .41.192V12h6a4 4 0 0 0 3.585-5.777.5.5 0 0 1 .225-.67Z'/>
                                </svg>
                            </a>
                            </div>
                            <div class='tooltip-container'>
                            <span class='tooltip-text'>Ingresar</span>
                            <a href='#!' class='btn btn-info' id='${idProd}' onclick='ingresoProd(this.id)'>
                                <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-box-arrow-in-right' viewBox='0 0 16 16'>
                                <path fill-rule='evenodd' d='M6 3.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 0-1 0v2A1.5 1.5 0 0 0 6.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-8A1.5 1.5 0 0 0 5 3.5v2a.5.5 0 0 0 1 0z'/>
                                <path fill-rule='evenodd' d='M11.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H1.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z'/>
                                </svg>
                            </a>
                            </div>
                            <div class='tooltip-container'>
                            <span class='tooltip-text'>Salida</span>
                            <a href='#!' class='btn btn-danger' id='${idProd}' onclick='salidaProd(this.id)'>
                                <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-box-arrow-up' viewBox='0 0 16 16'>
                                <path fill-rule='evenodd' d='M3.5 6a.5.5 0 0 0-.5.5v8a.5.5 0 0 0 .5.5h9a.5.5 0 0 0 .5-.5v-8a.5.5 0 0 0-.5-.5h-2a.5.5 0 0 1 0-1h2A1.5 1.5 0 0 1 14 6.5v8a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 14.5v-8A1.5 1.5 0 0 1 3.5 5h2a.5.5 0 0 1 0 1z'/>
                                <path fill-rule='evenodd' d='M7.646.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 1.707V10.5a.5.5 0 0 1-1 0V1.707L5.354 3.854a.5.5 0 1 1-.708-.708z'/>
                                </svg>
                            </a>
                            </div> - 
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

function traspasaProd(a){
    
    //verificamos el producto
    let datos = new FormData();

    datos.append('getDataArti',a);

    fetch("../includes/operacionesArti.php",{
        method: 'POST',
        body: datos
    }).then(function(res){
        return res.json();
    }).then(function(result){
        // console.log(result);
        if(result.status == "ok"){
            //verificamos si es un producto chip
            if(result.data[0].esChip == 1){
                const myModal = new bootstrap.Modal(document.getElementById('modalTraspasoChips'),{
                    keyboard:false
                })
                //es un producto normal
                myModal.show();
                //insertamos los datos auxiliares    
                document.getElementById('nombreProdTraspasoChip').innerHTML = "Traspasar "+result.data[0].nombreArticulo;
                document.getElementById('artiTraspasoChip').value = result.data[0].idArticulo;
            }else{
                const myModal = new bootstrap.Modal(document.getElementById('modalTraspasos'),{
                    keyboard:false
                })
                //es un producto normal
                myModal.show();
                //insertamos los datos auxiliares    
                document.getElementById('nombreProd').innerHTML = "Traspasar "+result.data[0].nombreArticulo;
                document.getElementById('artiTraspaso').value = result.data[0].idArticulo;    
            }
            
        
        }else{
            //ocurrio un error
            Swal.fire({
                title: 'Ha ocurrido un error',
                text: result.mensaje,
                icon: 'error'
            })
        }
    })

    
}

function ingresoProd(a){
//verificamos el producto
    let datos = new FormData();

    datos.append('getDataArti',a);

    fetch("../includes/operacionesArti.php",{
        method: 'POST',
        body: datos
    }).then(function(res){
        return res.json();
    }).then(function(result){
        // console.log(result);
        if(result.status == "ok"){
            const myModal = new bootstrap.Modal(document.getElementById('modalIngreso'),{
                keyboard:false
            })

            if(result.data[0].esChip == 1){
                const myModal = new bootstrap.Modal(document.getElementById('modalIngresoChips'),{
                    keyboard:false
                })
                //es un producto normal
                myModal.show();
                //insertamos los datos auxiliares    
                document.getElementById('nombreProdIngresoChip').innerHTML = "Ingresar "+result.data[0].nombreArticulo;
                document.getElementById('artiIngresoChip').value = result.data[0].idArticulo;
            }else{
                myModal.show();
                //insertamos los datos auxiliares
                document.getElementById('nombreProdIngreso').innerHTML = "Ingresar "+result.data[0].nombreArticulo;
                document.getElementById('artiIngreso').value = result.data[0].idArticulo;
            }
            

        }else{
            //ocurrio un error
            Swal.fire({
                title: 'Ha ocurrido un error',
                text: result.mensaje,
                icon: 'error'
            })
        }
    })
}

function salidaProd(a){
    //verificamos si es un chip o articulo normal
    let datos = new FormData();

    datos.append('getDataArti',a);

    fetch("../includes/operacionesArti.php",{
        method: 'POST',
        body: datos
    }).then(function(res){
        return res.json();
    }).then(function(result){
        // console.log(result);
        if(result.status == "ok"){

            if(result.data[0].esChip == 1){
                //abrimos el modal apra salida de chips

                const myModal = new bootstrap.Modal(document.getElementById('modalSalidaChipProd'),{
                     keyboard:false
                })
                //es un producto normal
                myModal.show();
                //insertamos los datos auxiliares    
                document.getElementById('nombreProdSalidaChip').innerHTML = "Salida de "+result.data[0].nombreArticulo;
                document.getElementById('artisalidaChipProd').value = result.data[0].idArticulo;
            }else{
                const myModal = new bootstrap.Modal(document.getElementById('modalSalidaProd'),{
                    keyboard:false
                })
                myModal.show();
                //insertamos los datos auxiliares
                document.getElementById('nombreProdSalida').innerHTML = "Salida de "+result.data[0].nombreArticulo;
                document.getElementById('artisalidaProd').value = result.data[0].idArticulo;
            }
            

        }else{
            //ocurrio un error
            Swal.fire({
                title: 'Ha ocurrido un error',
                text: result.mensaje,
                icon: 'error'
            })
        }
    })
}//fin funcion salida mercancia

function sucVeri(){
    //funcion par averificar entre traspasos de sucursales
    let sucOrigen = document.getElementById('sucOrigen').value;
    let sucDestino = document.getElementById('sucDestino').value;
    // console.log(sucOrigen);
    // console.log(sucDestino);

    if(sucOrigen != "" && sucDestino != ""){
        if(sucDestino == sucOrigen){
            Swal.fire({
                title: 'Sucursales Invalidas',
                text: 'No puedes traspasar en la misma sucursal',
                icon: 'error'
            }).then(function(){
                document.getElementById('sucDestino').value = "";
            })
        }
    }
}

let btnTraspaso = document.getElementById('btnExecuteTraspaso');
btnTraspaso.addEventListener('click', function(){
    //funcion para aplicar los traspasos entre sucursales
    let sucOrigen = document.getElementById('sucOrigen').value;
    let sucDestino = document.getElementById('sucDestino').value;
    let cantidad = document.getElementById('cantidadTraspaso').value;
    let articulo = document.getElementById('artiTraspaso').value;

    if(sucOrigen != sucDestino && cantidad > 0){
        //enviamos la solicitud
        let datos = new FormData();
        datos.append('sucOriTras',sucOrigen);
        datos.append('sucDesTras',sucDestino);
        datos.append('canTras',cantidad);
        datos.append('articuloTras',articulo);

        fetch("../includes/operacionesArti.php",{
            method: 'POST',
            body: datos
        }).then(function(res){
            return res.json();
        }).then(function(result){
            if(result.status == "ok"){
                Swal.fire({
                    title: 'Traspaso correcto',
                    text: 'Se traspaso correctamente el articulo',
                    icon: 'success'
                }).then(function(){
                    document.getElementById('cantidadTraspaso').value = "";
                })
            }else{
                Swal.fire({
                    title: 'Ha ocurrido un error',
                    text: result.mensaje,
                    icon: 'error'
                })
            }
        })
    }else{
        Swal.fire({
            title: 'Datos incorrectos',
            text: 'Asegurate de capturar corrctamente la informacion del traspaso',
            icon: 'erorr'
        })
    }
})


let btnIngresoProd = document.getElementById('btnExecuteIngreso');
btnIngresoProd.addEventListener('click', function(){

    let sucIngreso = document.getElementById('sucDestinoIngresoN').value;
    let cantIngreso = document.getElementById('cantidadIngreso').value;
    let artiIngreso = document.getElementById('artiIngreso').value;

    if(sucIngreso != "" && cantIngreso != "" && artiIngreso != ""){
        let datos = new FormData();
        datos.append('sucIngresoArti',sucIngreso);
        datos.append('cantIngreso',cantIngreso);
        datos.append('artiIngreso',artiIngreso);

        fetch("../includes/operacionesArti.php",{
            method: 'POST',
            body: datos,
        }).then(function(res){
            return res.json();
        }).then(function(result){
            console.log(result);
            if(result.status == "ok"){
                Swal.fire({
                    title: 'Ingreso Realizado',
                    text: 'Se realizo correctamente el ingreso',
                    icon: 'success'
                }).then(function(){
                    document.getElementById('cantidadIngreso').value = "";
                })
            }else{
                Swal.fire({
                    title: 'Ha ocurrido un error',
                    text: result.mensaje,
                    icon: 'error'
                })
            }
        })
    }else{
        Swal.fire({
            title: 'Datos incorrectos',
            text: 'Asegurate de indicar una sucursal y cantidad correcta',
            icon: 'error'
        })
    }
})

let campoChipTraspaso = document.getElementById('chipIngresoCodigo');
campoChipTraspaso.addEventListener('change',function(){
    let sucDestino = document.getElementById('sucDestinoChip').value;
    let codigoDestino = document.getElementById('chipIngresoCodigo').value;

    if(sucDestino > 0 && codigoDestino.length > 0){
        //enviamos el traspaso
        let prodData = document.getElementById('artiTraspasoChip').value;
        let sucDestinoChip = document.getElementById('sucDestinoChip').value;
        let chipIngresoCodigo = document.getElementById('chipIngresoCodigo').value;
        let datos = new FormData();
        datos.append('prodTraspasoChip',prodData);
        datos.append('sucDestinoChip',sucDestinoChip);
        datos.append('chipIngresoCodigo',chipIngresoCodigo);

        fetch("../includes/operacionesArti.php",{
            method: 'POST',
            body: datos
        }).then(function(res){
            return res.json();
        }).then(function(result){
            console.log(result);
            if(result.status == "ok"){
                Swal.fire({
                    title: 'Articulo Traspasado',
                    text: 'Se traspaso correctamente el articulo',
                    icon: 'success'
                }).then(function(){
                    document.getElementById('chipIngresoCodigo').value = "";
                })
            }else{
                Swal.fire({
                    title: 'Ha ocurrido un error',
                    text: result.mensaje,
                    icon: 'error'
                }).then(function(){
                    document.getElementById('chipIngresoCodigo').value = "";
                })
            }
        })
    }else{
        Swal.fire({
            title: 'Datos faltantes',
            text: 'Indique una sucursal o codigo validos',
            icon: 'error'
        })
    }
})


// Metodo para ingreso chips
let campoIngresoTraspaso = document.getElementById('codigoIngresoChip');
campoIngresoTraspaso.addEventListener('change',function(){
    let sucDestino = document.getElementById('sucDestinoChipIngreso').value;
    let codigoDestino = document.getElementById('codigoIngresoChip').value;

    if(sucDestino > 0 && codigoDestino.length > 0){
        //enviamos el traspaso
        let prodData = document.getElementById('artiIngresoChip').value;
        let sucDestinoChip = document.getElementById('sucDestinoChipIngreso').value;
        let chipIngresoCodigo = document.getElementById('codigoIngresoChip').value;
        let datos = new FormData();
        datos.append('articuloID',prodData);
        datos.append('sucursalChip',sucDestinoChip);
        datos.append('codigoChip',chipIngresoCodigo);

        fetch("../includes/modProducto.php",{
            method: 'POST',
            body: datos
        }).then(function(res){
            return res.json();
        }).then(function(result){
            console.log(result);
            if(result.status == "ok"){
                //Ingreso de chip realizado
                document.getElementById('codigoIngresoChip').value = "";

                document.getElementById('resIngresoChips').insertAdjacentHTML('afterbegin','Articulo: '+chipIngresoCodigo+' Ingresado.<br>');
                const Toast = Swal.mixin({
                    toast: true,
                    position: "top-end",
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                      toast.onmouseenter = Swal.stopTimer;
                      toast.onmouseleave = Swal.resumeTimer;
                    }
                  });
                  Toast.fire({
                    icon: "success",
                    title: "Ingreso Realizado"
                  });
            }else{
                Swal.fire({
                    title: 'Ha ocurrido un error',
                    text: result.mensaje,
                    icon: 'error'
                }).then(function(){
                    document.getElementById('chipIngresoCodigo').value = "";
                })
            }
        })
    }else{
        Swal.fire({
            title: 'Datos faltantes',
            text: 'Indique una sucursal o codigo validos',
            icon: 'error'
        })
    }
})


function cerrarModalIngresoChips(){
    document.getElementById('resIngresoChips').innerText = "";
    document.getElementById('sucDestinoChipIngreso').value ="";
}

function cerrarModalTraspasoChips(){
    document.getElementById('sucDestinoChip').value ="";
}

function cerrarModalIngreso(){
    document.getElementById('sucDestino').value ="";
    document.getElementById('cantidadIngreso').value ="";
}

function cerrarModalTraspaso(){
    document.getElementById('sucOrigen').value ="";
    document.getElementById('sucDestino').value ="";
    document.getElementById('cantidadTraspaso').value ="";
}

function setSalidaProd(){
    let sucSalida = document.getElementById('sucSalidaProd').value;
    let cantSalida = document.getElementById('cantidadSalidaProd').value;
    let artisalidaProd = document.getElementById('artisalidaProd').value;

    if(sucSalida != "" && cantSalida != ""){
        let datos = new FormData();
        datos.append('artisalidaProd',artisalidaProd);
        datos.append('sucSalidaArti',sucSalida);
        datos.append('cantSalidaArti',cantSalida);

        fetch("../includes/operacionesArti.php",{
            method: "POST",
            body: datos
        }).then(function(res){
            return res.json();
        }).then(function(result){
            if(result.status == "ok"){
                Swal.fire({
                    title: 'Salida Procesada',
                    text: 'Se proceso correctamente la salida',
                    icon: 'success'
                })
                document.getElementById('cantidadSalidaProd').value = "";
            }else{
                Swal.fire({
                    title: 'Ha ocurrido un error',
                    text: res.mensaje,
                    icon: 'error'
                })
            }
        })
        
    }else{
        Swal.fire({
            title: 'Datos incompletos',
            text: 'Asegurate de indicar una sucursal y cantidad valida',
            icon: 'error'
        })
    }

}

function cerrarModalSalidaChips(){
    document.getElementById('codigoSalidaProd').value = "";
    document.getElementById('artisalidaChipProd').value = "";

}

function setSalidaChip(){

    let codigoSalida = document.getElementById('codigoSalidaProd').value;
    let articuloIDChip = document.getElementById('artisalidaChipProd').value;
    if(codigoSalida != "" && articuloIDChip != ""){
        let datos = new FormData();
        datos.append('codigoSalidaChip',codigoSalida);
        datos.append('idProdChipSalida',articuloIDChip);

        fetch("../includes/operacionesArti.php",{
            method: 'POST',
            body: datos
        }).then(function(res){
            return res.json();
        }).then(function(result){
            if(result.status == "ok"){
                const Toast = Swal.mixin({
                    toast: true,
                    position: "top-end",
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                      toast.onmouseenter = Swal.stopTimer;
                      toast.onmouseleave = Swal.resumeTimer;
                    }
                  });
                  Toast.fire({
                    icon: "success",
                    title: "Salida procesada"
                  });
                  document.getElementById('codigoSalidaProd').value = "";
            }else{
                Swal.fire({
                    title: 'Ha ocurrido un error',
                    text: result.mensaje,
                    icon: 'error'
                })
            }
        })
    }else{
        Swal.fire({
            title: 'Datos faltantes',
            text: 'Asegurate de indicar un codigo valido.',
            icon: 'error'
        })
    }
}//fin funcion set salida