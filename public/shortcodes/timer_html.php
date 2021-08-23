<?php
echo '<div class="timer '.$attributes['css_classes'].'">
	<div class="timerwrapper_">
		<div class="d-flex">
			<div style="margin:'.$attributes['hours_margin'].'" class="flex-fill text-center">
				<div style="padding:'.$attributes['boxs_padding'].'px;background-color:'.$attributes['hours_background_color'].';" class="h">
				 	<h1 style="font-family:'.$attributes['hours_font'].';color:'.$attributes['hours_font_color'].';margin:'.$attributes['hours_margin'].'">00</h1>
				 	<p style="font-family:'.$attributes['hours_label_font'].';color:'.$attributes['hours_label_font_color'].'">'.$attributes['hours_label'].'</p>
				 </div>
			</div>
			<div style="margin:'.$attributes['minutes_margin'].'" class="flex-fill text-center">
				<div style="padding:'.$attributes['boxs_padding'].'px;background-color:'.$attributes['minutes_background_color'].';" class="m">
				 	<h1 style="font-family:'.$attributes['minutes_font'].';color:'.$attributes['minutes_font_color'].';margin:'.$attributes['minutes_margin'].'">00</h1>
				 	<p style="font-family:'.$attributes['minutes_label_font'].';color:'.$attributes['minutes_label_font_color'].'">'.$attributes['minutes_label'].'</p>
				 </div>
			</div>
			<div style="margin:'.$attributes['seconds_margin'].'" class="flex-fill text-center">
				<div style="padding:'.$attributes['boxs_padding'].'px;background-color:'.$attributes['seconds_background_color'].';" class="s">
				 	<h1 style="font-family:'.$attributes['seconds_font'].';color:'.$attributes['seconds_font_color'].';margin:'.$attributes['seconds_margin'].'">00</h1>
				 	<p style="font-family:'.$attributes['seconds_label_font'].';color:'.$attributes['seconds_label_font_color'].'">'.$attributes['seconds_label'].'</p>
				 </div>
			</div>
		</div>
	</div>
</div>';