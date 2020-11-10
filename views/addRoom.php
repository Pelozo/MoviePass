<main class="">

<div style="margin-top:50px" class="container">
	<div class="row">
		<div class="col-sm-1"></div>
		<div class="col-sm-2"></div>		
		<div class="col-sm-6 bg-light boxStyle">
			<form name="theform" action="" method="POST">
                <input type="hidden" name="idCinema" value="<?=$idCinema?>">
                <input type="hidden" name="id" value="<?php if(isset($room)) echo $room->getId()?>">

                <div class="form-group">
                
                    <label >Nombre<span class="asteriskField">*</span></label>
                    <input class="form-control" name="name" type="text" value="<?php if(isset($room)) echo $room->getName()?>" size="20"><br>
                <div class="form-group">
                    <label>Capacidad<span class="asteriskField">*</span></label>
                    <input class="form-control" name="capacity" type="number" value="<?php if(isset($room)) echo $room->getCapacity()?>" min="0" size="20">
                </div>
                <div class="form-group">
                    <label>Precio de entrada<span class="asteriskField">*</span></label>
                    <input class="form-control" name="ticket" type="number" value="<?php if(isset($room)) echo $room->getPrice()?>"  min="0" size="20">
                </div>


                <?php if(isset($error)){?>
                    <?=$error?>
                <?php }?>

                <div class="form-group">
                    <div class="row">
                       <input class="btn btn-primary" type="submit"   value="Enviar" style="font-weight: bold">
	
                    </div>
                </div>
             </form>      
		</div>
		<div class="col-sm-1"></div>
		<div class="col-sm-2"></div>
    </div> 
</div> 





<!--

    
<div class="container">

<div class="row text-white">
    <div style="margin-top:50px" class="mainbox col-md-offset-3 col-sm-8 col-sm-offset-2">
        <div class="panel panel-info">
            <div class="panel-heading">
                <div class="panel-title">Agregar cine</div>
            </div>  

            <div class="panel-body" >
                <form method="post" action=""> 
                        <div id="div_id_name" class="form-group required">
                            <label for="name" class="control-label col-md-4  requiredField"> Nombre <span class="asteriskField">*</span> </label>
                            <div class="controls col-md-8 ">
                                <input class="input-md  textinput textInput form-control" id="name" maxlength="30" name="name" placeholder="Nombre del cine" style="margin-bottom: 10px" type="text" />
                            </div>
                        </div>                      

                        
                        <div id="div_id_capacity" class="form-group required">
                            <label for="capacity" class="control-label col-md-4  requiredField"> Capacidad <span class="asteriskField">*</span> </label>
                            <div class="controls col-md-8 ">
                                <input class="input-md form-control" id="capacity" name="capacity" placeholder="Capacidad" style="margin-bottom: 10px" type="number" min="0"/>
                            </div>     
                        </div>                        

                        <div id="div_id_ticket" class="form-group required">                            
                            <label for="ticket" class="control-label col-md-4  requiredField"> Valor de entrada <span class="asteriskField">*</span> </label>
                            <div class="controls col-md-8 ">
                                <input class="input-md form-control" id="ticket" name="ticket" placeholder="Valor de entrada" style="margin-bottom: 10px" type="number" min="0"/>
                            </div>     
                        </div>                       
                        

                        <div id="div_id_address1" class="form-group required">
                            <label for="address1" class="control-label col-md-4  requiredField">Dirección linea 1 <span class="asteriskField">*</span> </label>
                            <div class="controls col-md-8 ">
                                <input class="input-md textinput textInput form-control" id="address1" name="address1" type="text" placeholder="Dirección linea 1" style="margin-bottom: 10px">                        
                            </div>
                        </div>

                        <div id="div_id_address2" class="form-group">
                            <label for="address2" class="control-label col-md-4">Dirección linea 2</label>
                            <div class="controls col-md-8 ">
                                <input class="input-md textinput textInput form-control" id="address2" name="address2" type="text" placeholder="Dirección linea 2" style="margin-bottom: 10px">                        
                            </div>
                        </div>

                        <div id="div_id_city" class="form-group required">
                            <label for="city" class="control-label col-md-4 requiredField"> Ciudad / Pueblo <span class="asteriskField">*</span> </label>
                            <div class="controls col-md-8 ">
                                <input class="input-md form-control" id="city" name="city" placeholder="Ciudad o Pueblo" style="margin-bottom: 10px"/>
                            </div>     
                        </div>

                        <div id="div_id_province" class="form-group required">
                            <label for="province" class="control-label col-md-4  requiredField"> Provincia <span class="asteriskField">*</span> </label>
                            <div class="controls col-md-8 ">
                                <input class="input-md  textinput textInput form-control" id="province" maxlength="30" name="province" placeholder="Provincia" style="margin-bottom: 10px" type="text" />
                            </div>
                        </div>

                        <div id="div_id_postal" class="form-group required">
                            <label for="postal" class="control-label col-md-4  requiredField"> Código postal <span class="asteriskField">*</span> </label>
                            <div class="controls col-md-8 ">
                                <input class="input-md form-control" id="postal" name="postal" placeholder="Código postal" style="margin-bottom: 10px" type="number" min="0"/>
                            </div>     
                        </div>

                        <div class="form-group"> 
                            <div class="aab controls col-md-4 "></div>
                            <div class="controls col-md-8 ">
                                <input type="submit" name="Signup" value="Agregar" class="btn btn-primary btn btn-info" id="submit-id-signup" />
                            </div>
                        </div> 

                </form>
            </div>
        </div>
    </div> 



</div> 


-->
</main>
