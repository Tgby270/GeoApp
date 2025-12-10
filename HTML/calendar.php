<?php
	session_start();
	require_once '../PHP/getInfrastructuresFromDb.php';

// planning configuration
define('__P_START_TIME', 8);
define('__P_END_TIME', 18);
define('__P_UNIT', 60);

$w = Array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');

// init matrix
// ###########

// the matrix has its axis reversed: matrix[y][x] so it's easier to print it as an html table
// 1 means a cell exist (default value)
// 0 means the cell doesn't exist
// higher numbers are equal to rowspan values
$matrix = Array();

// matrix x axis
$x = sizeof($w);

// matrix y axis
$y = ((__P_END_TIME - __P_START_TIME) * 60) / __P_UNIT;

// populate matrix with default value (1)
for( $i = 0; $i <= $y; $i++ )
{
	for( $j = 0; $j <= $x; $j++ )
	{
		$matrix[$i][$j] = Array('state' => 1, 'content' => '&nbsp;', 'day_span' => 1); // cell exist, default content
	}
}

// populate titles
// ###############

// x axis
foreach( $w as $k => $v)
{
	$matrix[0][$k+1]['content'] = $v;
}

// y axis
for( $i = 1; $i <= $y; $i++)
{
	$t = ((($i-1)* __P_UNIT) + (__P_START_TIME * 60)) / 60;
	if( !is_float($t) )
	{
		$matrix[$i][0]['content'] = $t.':00';
	}
}

// take string or an integer and return it's value in minutes
// ie: time_to_min('8:45'), time_to_min(8)
function time_to_min($time)
{
	if( preg_match('/:/', $time) )
	{
		$t = explode(':', $time);
		$mn = $t[0]*60 + $t[1]; 
	} else {
		$mn = $time * 60;
	}
	return $mn;
}

// take a string, return day's x position
function day_to_x($day)
{
	global $w;

	$day = strtolower($day);
	$x = 1; // default to Monday
	foreach( $w as $k => $v )
	{
		if( $day == strtolower($v) ) {
			$x = $k+1;
			break;
		}
	}
	
	return $x;
}

// take a string or integer, return time's y position
function time_to_y($time)
{
	$mn = time_to_min($time);
	
	$y = $mn - __P_START_TIME * 60;
	$y = ($y / __P_UNIT) + 1;

	return $y;
}

// set an event on the calendar
function set_event($env)
{
	global $matrix;

	// Get day from date_debut (e.g., '2025-12-09' -> 'Tuesday')
	$day = date('l', strtotime($env->getDateDebut()));
	$x = day_to_x($day);
	$y = time_to_y($env->getHeureDebut());
	$duration = $env->getDuration();
	
	// Ensure duration is valid (between 15 minutes and 10 hours)
	if($duration < 15 || $duration > 600) {
		$duration = 60; // default to 1 hour
	}
	
	$size = $duration / __P_UNIT;
	
	// Make sure we don't go out of bounds
	if($y < 1 || $x < 1 || $y > count($matrix)) return;
	
	// Cap size to not exceed available rows
	$max_rows = count($matrix) - $y;
	if($size > $max_rows) {
		$size = $max_rows;
	}
	
	$matrix[$y][$x]['state'] = $size;
	$matrix[$y][$x]['content'] = $env->getLibelle();
	
	// Mark cells as used for multi-hour events
	if( $size > 1 )
	{
		for($i = 1; $i < $size; $i++)
		{
			if(isset($matrix[$y+$i][$x])) {
				$matrix[$y+$i][$x]['state'] = 0;
			}
		}
	}
}

if(isset($_GET['infid'])) {
	$inf_id = $_GET['infid'];
} else {
	$inf_id = 1; // default infrastructure id
}

if(is_numeric($inf_id)) {
	$ev = getCalendarEventsThisWeekDB($inf_id);
} else {
	$ev = getCalendarEventsThisWeekAPI($inf_id);
}

foreach( $ev as $e )
{
	set_event( $e );
}

// Handle form submission BEFORE any HTML output
$error_message = '';
if($_SERVER['REQUEST_METHOD'] === 'POST') {
	$day = $_POST['event_day'];
	$time = intval($_POST['event_time']);
	$duration = intval($_POST['event_duration']);
	$lib = $_POST['event_label'];

	$horraireFin = $time + ($duration);
	
	// Arrondir au nombre entier supérieur si ce n'est pas un entier
	$horraireFin = ceil($horraireFin);

	$uid = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

	$result = insertCalendarEvent($inf_id, $lib, $day, $day,  $time, $horraireFin, $uid);
	
	if($result === true) {
		header("Location: calendar.php?infid=" . $inf_id);
		exit();
	} else {
		$error_message = $result;
	}
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Planning</title>
	<link rel="stylesheet" href="../CSS/calendar.css">
	<link rel="stylesheet" href="../CSS/header.css">
</head>
<body>
	<?php include('header.php'); ?>

	<div class="calendar-container">

		<?php if(!empty($error_message)): ?>
			<div style="background: #ffe6e6; color: #d32f2f; padding: 15px; margin: 20px; border-radius: 8px; border: 1px solid #d32f2f;">
				⚠️ <?php echo htmlspecialchars($error_message); ?>
			</div>
		<?php endif; ?>

		<div class="calendar-header">
			<h2>Planning pour la semaine du <?php echo date('d M Y'); ?></h2>
		</div>

	<!-- Planning Grid Table -->
	<div class="calendar-grid planning-grid">
		<?php foreach( $matrix as $y => $col ): ?>
			<?php foreach( $col as $x => $row ): ?>
				<?php if( $row['state'] !== 0 ): ?>
					<div class="calendar-day planning-cell <?php echo ($y === 0 || $x === 0) ? 'planning-header' : ''; ?> <?php echo ($row['content'] !== '&nbsp;' && $y !== 0 && $x !== 0) ? 'has-event' : ''; ?>" style="grid-row: span <?php echo $row['state'] > 1 ? $row['state'] : 1; ?>;">
						<?php echo $row['content']; ?>
					</div>
				<?php endif; ?>
			<?php endforeach; ?>
		<?php endforeach; ?>

		</div>

		<button id = "reservation-button">Passer une réservation</button>

		<!-- Modal Backdrop -->
		<div id="modal-backdrop" class="modal-backdrop"></div>

		<!-- Add Event Form Modal -->
		<div id="schedule-modal" class="schedule-form modal">
			<div class="modal-content">
				<div class="modal-header">
					<h3>Passez une réservation</h3>
					<button type="button" class="modal-close">&times;</button>
				</div>
				<form method="POST" action="">
					<div class="form-group">
						<label for="event-day">Jour</label>
						<input type="date" id="event-day" name="event_day" placeholder="Ex: dd-mm-yyyy" required>
					</div>

					<div class="form-group">
						<label for="event-time">Heure</label>
						<input type="number" id="event-time" name="event_time" placeholder="Ex: 8" required>
					</div>

					<div class="form-group">
						<label for="event-label">Libelle</label>
						<input type="text" id="event-label" name="event_label" placeholder="Ex: Réunion" required>
					</div>

					<div class="form-group">
						<label for="event-duration">Durée (heures)</label>
						<input type="number" id="event-duration" name="event_duration" placeholder="Ex: 1" required>
					</div>

					<div class="form-actions">
						<button type="submit" class="btn-submit">Créer la réservation</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<script>
		const modal = document.getElementById('schedule-modal');
		const backdrop = document.getElementById('modal-backdrop');
		const button = document.getElementById('reservation-button');
		const closeBtn = document.querySelector('.modal-close');
		const btnCancel = document.querySelector('.btn-cancel');

		button.addEventListener('click', function() {
			modal.classList.add('active');
			backdrop.classList.add('active');
		});

		closeBtn.addEventListener('click', function() {
			modal.classList.remove('active');
			backdrop.classList.remove('active');
		});

		backdrop.addEventListener('click', function() {
			modal.classList.remove('active');
			backdrop.classList.remove('active');
		});
	</script>
</body>
</html> 

