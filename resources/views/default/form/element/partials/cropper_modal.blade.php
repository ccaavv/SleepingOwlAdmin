<!-- Cropper modal -->
<div class="modal fade" aria-labelledby="modalLabel" role="dialog" tabindex="-1" data-backdrop="static">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close pull-right" data-dismiss="modal" aria-label="Close">
					<i class="fa fa-times"></i>
				</button>
				<h5 class="modal-title" id="modalLabel">Выбрать область миниатюры</h5>
			</div>
			<div class="modal-body">
				<div>
					<img :src="image_crop" alt="" class="img-responsive">
				</div>
				<div class="row">
					<div class="col-xs-12">
						<hr>
						<div class="btn-group-vertical">
							<button type="button" class="btn btn-sm btn-primary" @click.prevent="setDragMode('move')"
									title="Режим - передвинуть">
								<span class="fa fa-arrows"></span>
							</button>
							<button type="button" class="btn btn-sm btn-primary" @click.prevent="setDragMode('crop')"
									title="Режим - вырезать">
								<span class="fa fa-crop"></span>
							</button>
						</div>
						<div class="btn-group-vertical">
							<button type="button" class="btn btn-sm btn-primary" @click.prevent="zoom(0.1)" title="Приблизить">
								<span class="fa fa-search-plus"></span>
							</button>
							<button type="button" class="btn btn-sm btn-primary" @click.prevent="zoom(-0.1)" title="Отдалить">
								<span class="fa fa-search-minus"></span>
							</button>
						</div>
						<div class="btn-group-vertical">
							<button type="button" class="btn btn-sm btn-primary" @click.prevent="rotate(-45)"
									title="Вращение против часовой стрелки(-45)">
								<span class="fa fa-rotate-left"></span>
							</button>
							<button type="button" class="btn btn-sm btn-primary" @click.prevent="rotate(45)"
									title="Вращение по часовой стрелке(45)">
								<span class="fa fa-rotate-right"></span>
							</button>
						</div>
						<div class="btn-group-vertical">
							<button type="button" class="btn btn-sm btn-primary" @click.prevent="scaleX()"
									title="Отразить по горизонтали">
								<span class="fa fa-arrows-h"></span>
							</button>
							<button type="button" class="btn btn-sm btn-primary" @click.prevent="scaleY()"
									title="Отразить по вертикали">
								<span class="fa fa-arrows-v"></span>
							</button>
						</div>
						<div class="btn-group-vertical">
							<button type="button" class="btn btn-sm btn-primary" @click.prevent="reset()" title="Сбросить">
								<span class="fa fa-refresh"></span>
							</button>
							<button type="button" class="btn btn-sm btn-primary" @click.prevent="clear()" title="Очистить">
								<span class="fa fa-remove"></span>
							</button>
						</div>
						<div class="btn-group-vertical">
							<button type="button" class="btn btn-sm btn-primary" @click.prevent="move(-10, 0)"
									title="Сместить влево">
								<span class="fa fa-arrow-left"></span>
							</button>
							<button type="button" class="btn btn-sm btn-primary" @click.prevent="move(10, 0)"
									title="Сместить вправо">
								<span class="fa fa-arrow-right"></span>
							</button>
						</div>
						<div class="btn-group-vertical">
							<button type="button" class="btn btn-sm btn-primary" @click.prevent="move(0, -10)"
									title="Сместить вверх">
								<span class="fa fa-arrow-up"></span>
							</button>
							<button type="button" class="btn btn-sm btn-primary" @click.prevent="move(0, 10)"
									title="Сместить вниз">
								<span class="fa fa-arrow-down"></span>
							</button>
						</div>
						<div class="btn-group pull-right">
							<button type="button" class="btn btn-success" @click.prevent="crop()" title="Вырезать">
								<span class="fa fa-check"></span>
							</button>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-xs btn-default" data-dismiss="modal">Закрыть</button>
			</div>
		</div>
	</div>
</div>