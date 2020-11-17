<?php include(VIEWS_PATH."header.php"); ?>
<div class="table-responsive">
    <div class="table-wrapper">
        <div class="table-title">
        <div class="col-sm-8"><h1>Funcion Seleccionada</h1></div>
            <div class="row">
                <div class="col-sm-8"><h2><?=$show->getMovie()->getTitle()?></h2></div>
                <div class="col-sm-8"><img src="<?=$show->getMovie()->getImg()?>" style="margin:2em 0 2em 0" width="200px" height="300px"></img></div>
            </div>
        </div>
        <table id="showTable" class="table table-bordered">
        <col style="width:20%">
	    <col style="width:20%">
        <col style="width:10%">
        <col style="width:10%">
        <col style="width:40%">
            <thead>
                <tr>
                    <th>Cine</th>
                    <th>Sala</th>
                    <th>Precio</th>
                    <th>Capacidad de la Sala</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?=$show->getRoom()->getCinema()->getName()?></td>
                    <td><?=$show->getRoom()->getName()?></td>
                    <td><?=$show->getRoom()->getPrice()?></td>
                    <td><?=$show->getRoom()->getCapacity()?></td>
                    <td><?=$show->getDatetime()?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<div style="margin-bottom:100px" class="container">
	<div class="row">
		<div class="col-sm-1"></div>
		<div class="col-sm-2"></div>	
		<div class="col-sm-6 bg-light boxStyle">
			<form name="theform" action="<?=FRONT_ROOT?>purchase/makePurchase" method="POST">
                <h2>Finalizar Compra</h2>
                <?php if(!isset($purchase)){ ?>
                <input name="idShow" type="hidden" value="<?=$show->getId()?>">
                <div class="form-group">
                    <label>Seleccione cantidad de Entradas</label>
                    <select name="ticketsQuantity" id="tickets" onchange="updateTotal()">
                    <?php
                        for($i = 1; $i <= $availableCapacity; $i++){ ?>
                            <option value="<?=$i?>"><?=$i?></option>
                    <?php } ?>
                    </select>
     
                </div>
                <div class="form-group">
                    <label>Nro. de Tarjeta</label>
                    <input class="form-control" name="cardNumber" type="text" size="20">
                </div>
                <div class="form-group">
                    <label>Vencimiento</label>
                    <input class="form-control" name="cardExp" type="date" size="20">
                </div>
                <div class="form-group">
                    <label>Nombre y Apellido</label>
                    <input class="form-control" name="cardName" type="text" value="<?php if(isset($profile)) echo $profile->getFirstName() . " " . $profile->getLastName()?>" size="20">                        
                </div>
                <div class="form-group">
                    <label>D.N.I</label>
                    <input class="form-control" name="cardDni" type="text" value="<?php if(isset($profile)) echo $profile->getDni()?>" size="20">                        
                </div>
                <div class="form-group">
                    <label>Codigo de Descuento</label>
                    <input class="form-control" name="discount" type="text" size="20">                        
                </div>
                <div class="form-group">
                    <label>Total</label>
                    <input class="form-control" name="total" id="total" type="text" size="20" disabled>                        
                </div>
                <div class="form-group">
                    <div class="row">
                       <input class="btn btn-primary" type="submit"  value="Comprar" style="font-weight: bold">
                    </div>
                </div>
                <?php } ?>
            </form>
            <?php if(isset($message)){
                echo $message;
                ?> <a href="<?=FRONT_ROOT?>">Volver al Inicio</a>
            <?php } ?>     
		</div>
		<div class="col-sm-1"></div>
		<div class="col-sm-2"></div>
    </div> 
</div>

<script>
$(document).ready(function(){
    updateTotal();
});

function updateTotal(){
    var tickets = document.getElementById("tickets").value;
  $("#total").val("$" + tickets * <?=$show->getRoom()->getPrice();?>);
}

</script>
<?php include(VIEWS_PATH."footer.php"); ?>