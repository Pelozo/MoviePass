<table id="showTable" class="table table-bordered">
        <col style="width:10%">
	    <col style="width:20%">
        <col style="width:20%">
        <col style="width:50%">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Pelicula</th>
                    <th>Numero entrada</th>
                    <th>QR</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($tickets as $ticket){?>
                <tr>
                    <td><?=$ticket->getPurchase()->getShow()->getDatetime();?></td>
                    <td><?=$ticket->getPurchase()->getShow()->getMovie()->getTitle();?></td>
                    <td><?=$ticket->getTicket_number();?></td>
                    <td><img src="<?=$ticket->getQr();?>" width="200px"></td>
                </tr> 
                <?php } ?>
            </tbody>
</table>