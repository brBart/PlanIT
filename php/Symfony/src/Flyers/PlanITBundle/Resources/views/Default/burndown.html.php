<?php $view->extend('PlanITBundle::base.html.php') ?>

<?php $view['slots']->start('body') ?>
    	<div id="content">
    		<form id="form_burndown" action="<?php echo $view['router']->generate('PlanITBundle_burndownFeedback'); ?>" method="get">
	    		<table width="100%" border="0" cellspacing="5">
		    		<tr>
		    			<td style="width: 150px;"><label for="idproject">Choose your project :</label></td>
		    			<td>
			    			<select id="idproject" name="idproject">
				    			<?php foreach ($projects as $p) : ?>
				    			<option <?php echo ($p->getIdproject() == $idproject) ? 'selected="selected"' : '' ;?> value="<?php echo $p->getIdproject(); ?>" onclick="$('#form_gantt').submit()"><?php echo $p->getName(); ?></option>
				    			<?php endforeach; ?>
				    		</select>
		    			</td>
		    		</tr>
		    	</table>
    		</form>
    		
    		<div id="chartdiv" style="height:550px;width:100%;"></div>
    	</div>
    	<?php if (isset($graphic) && !empty($graphic)) : ?>
    	<script type="text/javascript">
    		$.jqplot('chartdiv', <?php echo $graphic; ?>, {axes:{xaxis:{renderer:$.jqplot.DateAxisRenderer}}});
    	</script>
    	<?php endif; ?>
<?php $view['slots']->stop() ?>