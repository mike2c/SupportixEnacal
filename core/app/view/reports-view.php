<div class="row">
	<div class="col-md-12">
		<div class="card">
			<div class="card-header" data-background-color="blue">
				<h4 class="title">Reporte de Tickets</h4>
			</div>
			<div class="card-content table-responsive">


				<form class="form-horizontal" role="form">
					<input type="hidden" name="view" value="reports">
					<?php
					$projects = ProjectData::getAll();
					$priorities = PriorityData::getAll();
					$statuses = StatusData::getAll();
					$kinds = KindData::getAll();
					?>

					<div class="form-group">

						<div class="col-lg-3">
							<div class="input-group">
								<span class="input-group-addon"><i class="fa fa-male"></i></span>
								<select name="project_id" class="form-control">
									<option value="">PROJECTO</option>
									<?php foreach ($projects as $p) : ?>
										<option value="<?php echo $p->id; ?>" <?php if (isset($_GET["project_id"]) && $_GET["project_id"] == $p->id) {
																					echo "selected";
																				} ?>><?php echo $p->name; ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
						<div class="col-lg-3">
							<div class="input-group">
								<span class="input-group-addon"><i class="fa fa-support"></i></span>
								<select name="priority_id" class="form-control">
									<option value="">PRIORIDAD</option>
									<?php foreach ($priorities as $p) : ?>
										<option value="<?php echo $p->id; ?>" <?php if (isset($_GET["priority_id"]) && $_GET["priority_id"] == $p->id) {
																					echo "selected";
																				} ?>><?php echo $p->name; ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
						<div class="col-lg-3">
							<div class="input-group">
								<span class="input-group-addon">INICIO</span>
								<input type="date" name="start_at" value="<?php if (isset($_GET["start_at"]) && $_GET["start_at"] != "") {
																				echo $_GET["start_at"];
																			} ?>" class="form-control" placeholder="Palabra clave">
							</div>
						</div>
						<div class="col-lg-3">
							<div class="input-group">
								<span class="input-group-addon">FIN</span>
								<input type="date" name="finish_at" value="<?php if (isset($_GET["finish_at"]) && $_GET["finish_at"] != "") {
																				echo $_GET["finish_at"];
																			} ?>" class="form-control" placeholder="Palabra clave">
							</div>
						</div>

					</div>
					<div class="form-group">

						<div class="col-lg-3">
							<div class="input-group">
								<span class="input-group-addon">ESTADO</span>
								<select name="status_id" class="form-control">
									<?php foreach ($statuses as $p) : ?>
										<option value="<?php echo $p->id; ?>" <?php if (isset($_GET["status_id"]) && $_GET["status_id"] == $p->id) {
																					echo "selected";
																				} ?>><?php echo $p->name; ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
						<div class="col-lg-3">
							<div class="input-group">
								<span class="input-group-addon">TIPO</span>
								<select name="kind_id" class="form-control">
									<?php foreach ($kinds as $p) : ?>
										<option value="<?php echo $p->id; ?>" <?php if (isset($_GET["kind_id"]) && $_GET["kind_id"] == $p->id) {
																					echo "selected";
																				} ?>><?php echo $p->name; ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
						<div class="col-lg-6" style="display:flex;">
							<button class="btn btn-primary btn-block">Procesar</button>
							<!-- <button type="button" class="btn-icon" onclick="exportTiketsToExcel()">
								<img src="https://findicons.com/files/icons/2795/office_2013_hd/2000/excel.png" alt="">
							</button> -->
						</div>
					</div>
				</form>

				<?php
				$users = array();
				if ((isset($_GET["status_id"]) && isset($_GET["kind_id"]) && isset($_GET["project_id"]) && isset($_GET["priority_id"]) && isset($_GET["start_at"]) && isset($_GET["finish_at"])) && ($_GET["status_id"] != "" || $_GET["kind_id"] != "" || $_GET["project_id"] != "" || $_GET["priority_id"] != "" || ($_GET["start_at"] != "" && $_GET["finish_at"] != ""))) {
					$sql = "select * from ticket where ";
					if ($_GET["status_id"] != "") {
						$sql .= " status_id = " . $_GET["status_id"];
					}

					if ($_GET["kind_id"] != "") {
						if ($_GET["status_id"] != "") {
							$sql .= " and ";
						}
						$sql .= " kind_id = " . $_GET["kind_id"];
					}


					if ($_GET["project_id"] != "") {
						if ($_GET["status_id"] != "" || $_GET["kind_id"] != "") {
							$sql .= " and ";
						}
						$sql .= " project_id = " . $_GET["project_id"];
					}

					if ($_GET["priority_id"] != "") {
						if ($_GET["status_id"] != "" || $_GET["project_id"] != "" || $_GET["kind_id"] != "") {
							$sql .= " and ";
						}

						$sql .= " priority_id = " . $_GET["priority_id"];
					}



					if ($_GET["start_at"] != "" && $_GET["finish_at"]) {
						if ($_GET["status_id"] != "" || $_GET["project_id"] != "" || $_GET["priority_id"] != "" || $_GET["kind_id"] != "") {
							$sql .= " and ";
						}

						$sql .= " ( date_at >= \"" . $_GET["start_at"] . "\" and date_at <= \"" . $_GET["finish_at"] . "\" ) ";
					}

					$users = TicketData::getBySQL($sql);
				} else {
					$users = TicketData::getAll();
				}
				if (count($users) > 0) {
					// si hay usuarios
					$_SESSION["report_data"] = $users;
				?>
					<div class="panel panel-default">
						<div class="panel-heading">Reportes</div>
						<table class="table table-bordered table-hover" id="tickets">							
							<tbody>
								<tr>
									<th>Asunto</th>
									<th>Proyecto</th>
									<th>Tipo</th>
									<th>Categoria</th>
									<th>Prioridad</th>
									<th>Estado</th>
									<th>Creado el</th>
									<th>Ultima Modificación</th>
								</tr>
								<?
									$total = 0;
									foreach ($users as $user) {

										$project = $user->getProject();
										$medic = $user->getPriority();
										$lastModificationDate = (is_null($user->updated_at) || empty($user->updated_at)) ? ' - ' : $user->updated_at;

										// print rows
										echo "
											<tr>
												<td>{$user->title}</td>
												<td>{$project->name}</td>
												<td>{$user->getKind()->name}</td>
												<td>{$user->getCategory()->name}</td>
												<td>{$medic->name}</td>
												<td>{$user->getStatus()->name}</td>
												<td class='text-center'>{$user->created_at}</td>
												<td class='text-center'>{$lastModificationDate}</td>
											</tr>
										";			
										// end print					
									}
								?>
							</tbody>
						</table>

						<?
						
							} else {
								echo "<p class='alert alert-danger'>No hay tickets</p>";
							}
						?>
					</div>
			</div>
		</div>	
	</div>

	<script type="text/javascript">
		// function exportTiketsToExcel() {

		// 	const today = new Date().toDateString();
		// 	const table = document.querySelector("#tickets");
		// 	const filename = `tickets_report_${today}`;

		// 	console.log(table);
		// 	exportTable(table, filename, 'Tickets');
		// }
		TableExport.prototype.formatConfig.xlsx.buttonContent = "XLSX";
		TableExport.prototype.formatConfig.csv.buttonContent = "CSV";
		TableExport.prototype.formatConfig.txt.buttonContent = "TXT";

		TableExport(document.getElementsByTagName("table"), {
			headers: true,                      // (Boolean), display table headers (th or td elements) in the <thead>, (default: true)
			footers: true,                      // (Boolean), display table footers (th or td elements) in the <tfoot>, (default: false)
			formats: ["xlsx", "csv", "txt"],    // (String[]), filetype(s) for the export, (default: ['xlsx', 'csv', 'txt'])
			filename: "id",                     // (id, String), filename for the downloaded file, (default: 'id')
			bootstrap: true,                   // (Boolean), style buttons using bootstrap, (default: true)
			exportButtons: true,                // (Boolean), automatically generate the built-in export buttons for each of the specified formats (default: true)
			position: "top",                 // (top, bottom), position of the caption element relative to table, (default: 'bottom')
			ignoreRows: null,                   // (Number, Number[]), row indices to exclude from the exported file(s) (default: null)
			ignoreCols: null,                   // (Number, Number[]), column indices to exclude from the exported file(s) (default: null)
			trimWhitespace: true,               // (Boolean), remove all leading/trailing newlines, spaces, and tabs from cell text in the exported file(s) (default: false)
			RTL: false,                         // (Boolean), set direction of the worksheet to right-to-left (default: false)
			sheetname: "id"                     // (id, String), sheet name for the exported spreadsheet, (default: 'id')
		});
	</script>