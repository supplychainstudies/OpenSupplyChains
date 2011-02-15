<?php $counter= 0;?>
<div class="supplychain-list" style="list-style-type: none;">
 <?php foreach($supplychains as $supplychain): ?>
  <div id="<?= $counter?>" style="margin-bottom: 10px; padding-top: 10px; padding-bottom: 10px;">
   <div class="map-thumb-nail" style="width: 70px; float: left; padding-right: 10px;">
    <a href="map/view/<?= $supplychain['id']?>">
     <img style="width: 100%;" src="map/static/<?= $supplychain['id']?>" />
    </a>
   </div>
   <div class="attributes">
   <div>
    <a href="map/view/<?= $supplychain['id']?>"><span class="supplychain-name" style="font-size: 15px; font-weight: 700;"><?= $supplychain['key']?></span></a>
   </div>
   <span class="owner-name">Created by: <?= $supplychain['owner']?></span>
   <span class="stop-count">Total stops: <?= $supplychain['stops']?></span>
   <span class="hop-count">Total hops: <?= $supplychain['hops']?></span>
  </div>
 </div>
 <br/>     
 <?php $counter++;?>
 <?php endforeach; ?>
</div>


