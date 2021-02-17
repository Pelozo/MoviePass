<?php include(VIEWS_PATH."header.php"); ?>






<div class="container-fluid">
    <div class="row ">
            <div class="col-4 mt-5">

                <div class="row d-flex justify-content-center  text-center">
                    <div class="col-12"> 
                        <h1><?=$show->getMovie()->getTitle()?></h1> 
                    </div>
                    <div class="col-6"> 
                        <img class= "img-fluid max-width: 100%"  src="<?=$show->getMovie()->getImg()?>"/>
                    </div>
                    <div class="col-8 mt-1">
                        
                        <p class="h4 font-weight-bold">
                            Cine: <?=$show->getRoom()->getCinema()->getName()?>
                        </p>
                        <p class="h6 font-weight-bold">
                            <?=$show->getRoom()->getCinema()->getAddress()?>
                        </p>

                        <p class="h6 font-weight-bold mt-2">
                            <?=$show->getRoom()->getName()?>
                        </p>

                        <p class="h6 font-weight-bold mt-4">
                            Horario de función: <?=utf8_encode(strftime("%A %d de %B del %Y a las %H:%M", strtotime($show->getDatetime())));?>

                        </p>

                    </div>
                </div>

                
            </div>
        <div class="col-md-8 mt-5">

            <div class="container">
            <div class="row">
                <div class="col-sm-12 bg-light boxStyle">
                    <?php if(isset($discountMessage)){?>
                        <div class="alert alert-info" role="alert">
                            <?=$discountMessage;?>
                        </div>
                    <?php } ?>

                    <form name="theform" action="<?=FRONT_ROOT?>purchase/makePurchase" method="POST">
                    <?php if(!isset($purchase)){ ?>

                        <h2>Finalizar Compra</h2>
                        
                        <input name="idShow" type="hidden" value="<?=$show->getId()?>">
                        <div class="form-group">
                            <label>Seleccione cantidad de Entradas</label>
                            <select class="form-control" name="ticketsQuantity" id="tickets" onchange="updateTotal()">
                            <?php
                                for($i = 1; $i <= $availableCapacity; $i++){ ?>
                                    <option value="<?=$i?>"><?=$i?></option>
                            <?php } ?>
                            </select>
            
                        </div>
                            <div class="row">
                                <div class="form-group col-sm-8">
                                    <div class="form-group">
                                        <label>Nro. de Tarjeta</label>
                                        <input class="form-control" name="cardNumber" type="text" size="20" required>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                <div class="form-group">
                                    <label>CVV/CVC</label>
                                    <input class="form-control" name="cardCode" type="number" maxlength="3" required>
                                </div>
                            </div>
                        </div>


                        <div class="form-group">
                            <label>Vencimiento</label>
                            <input class="form-control" name="cardExp" type="month" size="20" min="<?=date('Y-m');?>" required>
                        </div>
                        <div class="form-group">
                            <label>Nombre y Apellido</label>
                            <input class="form-control" name="cardName" type="text" value="<?php if(isset($profile)) echo $profile->getFirstName() . " " . $profile->getLastName()?>" size="20" required>                        
                        </div>
                        <div class="form-group">
                            <label>D.N.I</label>
                            <input class="form-control" name="cardDni" type="text" value="<?php if(isset($profile)) echo $profile->getDni()?>" size="20" required>                        
                        </div>
                        <div class="form-group">
                            <label>Codigo de Descuento</label>
                            <input class="form-control" name="discount" type="text" size="20" required>                        
                        </div>
                        <div class="form-group">
                            <label id="lblTotal">Total</label>
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
                        ?> <br><a href="<?=FRONT_ROOT?>">Volver al Inicio</a>
                    <?php } ?>     
                </div>
                <div class="col-sm-1"></div>
                <div class="col-sm-2"></div>
            </div>     
        </div>



    </div>
</div>
            
<script>

var discount = <?=$discount?>;

$(document).ready(function(){
    updateTotal();
});

function updateTotal(){
    var tickets = document.getElementById("tickets").value;
    
    if(tickets > 1 && discount > 0){        
        $("#total").val("$" + tickets * <?=$show->getRoom()->getPrice();?> * (1 - (discount/100)));
        $("#lblTotal").text("Total: (¡Con descuento del " + discount + "%!)");
    }else{
        $("#total").val("$" + tickets * <?=$show->getRoom()->getPrice();?>);
        $("#lblTotal").text("Total:");
    }
  
}

</script>






<?php include(VIEWS_PATH."footer.php"); ?>