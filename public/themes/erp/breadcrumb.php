<?php
$ruta = array();
if(!empty($this->breadcrumb)){
	extract($this->breadcrumb);
}
?>
<div class="row border-bottom white-bg" style="padding-top:6px; padding-bottom:6px;">
	<div class="col-xs-0 col-sm-4 col-md-6 col-lg-6">
		<h2 class="hidden-xs hidden-sm" style="margin:0;"><?php echo !empty($titulo) ? $titulo : ""; ?></h2>
                
<div class="col-xs-7 col-sm-8 col-md-6 col-lg-6">
       <?php if(!empty($ruta)): ?>
       <ol class="breadcrumb">
           <?php
           if(!empty($ruta)){
               foreach ($ruta AS $item) {                  
                   $status = $item["activo"] == true ? 'class="active"' : "";
                   
                    $textlink =  isset($item["url"]) && !empty($item["url"]) ? '<li '. $status .'><a href="'. base_url($item["url"]) .'">'. $item["nombre"] .'</a></li>' : '<li '. $status .'>'.$item["nombre"].'</li>';
                    echo $textlink;
                }
           }
           ?>
       </ol>
       <?php endif; ?>
       
   </div>
	</div>
    
	<div class="col-xs-12 col-sm-12 col-md-3 col-lg-4 col-xs-offset-0 col-sm-offset-0 col-md-offset-3 col-lg-offset-2">


		<?php if(isset($historial)): ?>
			<a href="javascript:" id="verHistorial">
				<i class="fa fa-history fa-2x"></i>
			</a>
		<?php endif; ?>
	<?php

		if(!empty($menu))
		{
			$class = !empty($menu["clase"])? $menu["clase"]: "";

			?>
			<div id="moduloOpciones" class="btn-group btn-group-sm pull-right" style="margin:6px 12px 6px 0;">
				<?php
				//Veriifcar si el array de menu existe
				if(!empty($menu["nombre"])):
					if(preg_match("/#/i", $menu["url"])):  ?>

						<button class="btn btn-primary" type="button" id="<?php echo !empty($menu["url"]) ? base_url($menu["url"]) : ""; ?>" data-toggle="dropdown" aria-expanded="true"><?php echo !empty($menu["nombre"]) ? $menu["nombre"] : ""; ?></button>

					<?php elseif(preg_match("/\//i", $menu["url"])): ?>

						<a href="<?php echo !empty($menu["url"]) ? base_url($menu["url"]) : "#"; ?>" class="btn btn-primary"><?php echo !empty($menu["nombre"]) ? $menu["nombre"] : ""; ?></a>
					<?php elseif(preg_match("/javascript:/i", $menu["url"])): ?>

						<a href="<?php echo !empty($menu["url"]) ? $menu["url"] : "#"; ?>" class="btn btn-primary <?php echo !empty($menu["clase"])? $menu["clase"]: ""; ?>"><?php echo !empty($menu["nombre"]) ? $menu["nombre"] : ""; ?></a>
					<?php else: ?>

						<button class="btn btn-primary" type="button" data-toggle="dropdown" aria-expanded="true"><?php echo !empty($menu["nombre"]) ? $menu["nombre"] : ""; ?></button>

					<?php
					endif;
				endif;
				?>

				<?php if(!empty($menu["opciones"])): ?>
				<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
					<span class="caret"></span>
					<span class="sr-only">Toggle Dropdown</span>
				</button>
				<ul class="dropdown-menu <?php echo $class; ?>" >
					<?php
					if(!empty($menu["opciones"]))
					{
						foreach($menu["opciones"] AS $attr => $nombre_opcion)
						{
							$href = $attr;
							$hash = explode(',', $href);

							//Verificar si el atributo es un id
							$id = preg_match("/#/i", $attr) == true ? str_replace("#", "", $attr) : "";

							//Verificar si el atributo es una URL
							$url = preg_match("/\//i", $attr) == true ? $attr : "";

							echo '<li><a href="'. (!empty($url) ? base_url($url) : (preg_match("/ToggleTabs/i", $class) && !empty($hash[0]) ? $hash[0] : '')) .'" '. (!empty($id) && !preg_match("/ToggleTabs/i", $class) ? 'id="'. $id .'"' : "") .' '. (preg_match("/ToggleTabs/i", $class)  ? 'data-target="'. $href .'"' : '') .'>'. $nombre_opcion .'</a></li>';
						}
					}
					?>
				</ul>

				<?php endif; ?>

			</div>
			<?php
		}
		?>

		<?php if(!empty($filtro) && $filtro == true): ?>
		<div id="breadcrumbBtns" class="btn-group btn-group-sm hidden-xs pull-right" role="group" style="margin:6px 4px 6px 0;">
			<a href="#tabla" role="tab" data-toggle="tab" class="btn btn-primary active"><i class="fa fa-list"></i></a>
			<a href="#grid" role="tab" data-toggle="tab" class="btn btn-primary"><i class="fa fa-th-large"></i></a>
		</div>
		<?php endif; ?>

		<!-- Filtro Grupal de Botones -->
		<div id="filtroGroupBtns" class="btn-group btn-group-sm hidden-xs pull-right" role="group" style="margin:6px 4px 6px 0;">
		<?php
		    if(!empty($filtro_botones)){
				$cnt = 0;
				foreach ($filtro_botones AS $attr => $boton) {
					//$margin = $key == $cnt ? 'style="margin-right:4px;"' : "";
					$margin = "";

					//Verificar si el atributo es un id
					$id = preg_match("/#/i", $attr) == true ? str_replace("#", "", $attr) : "";

					//Verificar si el atributo es una URL
					$url = preg_match("/\//i", $attr) == true ? $attr : "";

					echo '<a href="'. (!empty($url) ? base_url($url) : "#") .'" '. (!empty($id) ? 'id="'. $id .'"' : "") .' class="btn btn-primary" '.$margin.'>'. $boton .'</a>' . "\n";
					$cnt++;
				}
			}
		?>
       	</div>
		<!-- /Filtro Grupal de Botones -->

	</div>
</div>
