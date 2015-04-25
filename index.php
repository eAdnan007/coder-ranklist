<?php
/*
Plugin Name: Coder Ranklist
Plugin URI: http://deviserweb.com
Description: Provides a ranklist of ACM programmers of LU.
Version: 1.0
Author: DeviserWeb
Author URI: http://deviserweb.com
License: Personal
*/

if( $_SERVER['SCRIPT_FILENAME'] == __FILE__ )
{
  header('Location: /');
  exit;
}


/**
 * Create cron job to update ranks everyday
 */
register_activation_hook( __FILE__, function(){
	wp_schedule_event( time(), 'daily', 'cr_update_ranklist' );
});


/**
 * Remove cron while uninstalling the plugin
 */
register_deactivation_hook( __FILE__, function(){
	wp_unschedule_event( wp_next_scheduled( 'cr_update_ranklist' ), 'cr_update_ranklist' );
});


/**
 * Create the admin page to maintain the list of coders
 */
add_action('admin_menu', function(){
	add_menu_page( 
		'Coder Rank List',
		'Rank List', 
		'publish_pages', 
		'coder-ranklist', 
		function(){
			if( isset( $_POST['submit'] ) && isset( $_POST['cr_nonce'] ) && wp_verify_nonce( $_POST['cr_nonce'], 'ranklist') ) {
				update_option( 'cr_coders', $_POST['coder'] );
				update_option( 'cr_formula', $_POST['point_formula'] );
			}
			?>
			<div class="wrap">
				<h2>LU ACM Coder's List</h2>

				<form action="" method="post">
					<table class="wp-list-table widefat fixed media" cellspacing="0">
						<thead>
							<tr>
								<th class="manage-column column-title name">Name</th>
								<th class="manage-column column-title sid">Student ID</th>
								<th class="manage-column column-title cf_handle">CF Handle</th>
								<th class="manage-column column-title tc_handle">TC Handle</th>
								<th class="manage-column column-title class_performance">Performance</th>
								<th class="manage-column column-title"><a href="#" class="add_coder add-new-h2">Add</a></th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th class="manage-column column-title name">Name</th>
								<th class="manage-column column-title sid">Student ID</th>
								<th class="manage-column column-title cf_handle">CF Handle</th>
								<th class="manage-column column-title tc_handle">TC Handle</th>
								<th class="manage-column column-title class_performance">Performance</th>
								<th class="manage-column column-title"><a href="#" class="add_coder add-new-h2">Add</a></th>
							</tr>
						</tfoot>
						<tbody id="the-list">
							<?php $coders = get_option('cr_coders', array()); ?>
							<?php if (!empty($coders)): ?>
								<?php foreach ($coders as $i => $coder): ?>
									<tr class="cr_list alternate author-self status-inherit" valign="top">
										<td class="name">
											<input type="text" name="coder[<?php echo $i; ?>][name]" name_format="coder[%d][name]" value="<?php echo $coder['name']; ?>">
										</td>
										<td class="sid">
											<input type="text" name="coder[<?php echo $i; ?>][sid]" name_format="coder[%d][sid]" value="<?php echo $coder['sid']; ?>">
										</td>
										<td class="cf_handle">
											<input type="text" name="coder[<?php echo $i; ?>][cf_handle]" name_format="coder[%d][cf_handle]" value="<?php echo $coder['cf_handle']; ?>">
										</td>
										<td class="tc_handle">
											<input type="text" name="coder[<?php echo $i; ?>][tc_handle]" name_format="coder[%d][tc_handle]" value="<?php echo $coder['tc_handle']; ?>">
										</td>
										<td class="class_performance">
											<input type="text" name="coder[<?php echo $i; ?>][class_performance]" name_format="coder[%d][class_performance]" value="<?php echo $coder['class_performance']; ?>">
										</td>
										<td class="remove_btn">
											<a href="#" class="add-new-h2 remove_coder">Remove</a>
										</td>
									</tr>
								<?php endforeach ?>
							<?php else: ?>
								<tr class="cr_list alternate author-self status-inherit" valign="top">
									<td class="name">
										<input type="text" name="coder[0][name]" name_format="coder[%d][name]">
									</td>
									<td class="sid">
										<input type="text" name="coder[0][sid]" name_format="coder[%d][sid]">
									</td>
									<td class="cf_handle">
										<input type="text" name="coder[0][cf_handle]" name_format="coder[%d][cf_handle]">
									</td>
									<td class="tc_handle">
										<input type="text" name="coder[0][tc_handle]" name_format="coder[%d][tc_handle]">
									</td>
									<td class="class_performance">
										<input type="text" name="coder[0][class_performance]" name_format="coder[%d][class_performance]">
									</td>
									<td class="remove_btn">
										<a href="#" class="add-new-h2 remove_coder">Remove</a>
									</td>
								</tr>
							<?php endif ?>
						</tbody>
					</table>
				
					<br>

					<div class="points">
						<label>Point Formula: </label>
						<input type="text" name="point_formula" class="regular-text" value="<?php echo get_option('cr_formula', '{CFR}*60/1000 + {TCR}*20/1500 + {CP}'); ?>">
						<br>
						<small class="desc">Complease the equation using the variables {CFR}, {TCR} &amp; {CP} for respectively Codeforce Rating, Top Coder Rating &amp; Class performance.</small>
					</div>

					<?php wp_nonce_field( 'ranklist', 'cr_nonce' ); ?>
					<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save"></p>
					<style>
					.add_coder {
						display: block;
						top: 0 !important;
						text-align: center;
					}
					.remove_btn {
						text-align: center;
					}
					.remove_btn a {
						display: block;
					}

					th.name {
						width: 30%;
					}
					th.sid {
						width: 20%;
					}

					#the-list input[type=text] {
						width: 100%;
					}
					</style>
					<script>
					jQuery(document).ready(function($){
						$('#the-list').addInputArea({
							area_var: '.cr_list',
							btn_add: '.add_coder',
							btn_del: '.remove_coder'
						});
					});
					</script>
				</form>
			</div>
			<?php
			
			do_action('cr_update_ranklist'); // Update ranklist when coder information is updated
		}, 
		'dashicons-chart-bar' );
});

/**
 * Queue javascripts in backend
 */
add_action( 'admin_enqueue_scripts', function(){
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 
		'jquery-add-input-area', 
		plugins_url( 'jquery.add-input-area.js', __FILE__ ), 
		array( 'jquery' ), 
		'4.7.1' );
});

/**
 * Queue javascripts in frontend
 */
add_action( 'wp_enqueue_scripts', function(){

	wp_enqueue_style( 'bootstrap', plugins_url( 'bootstrap.min.css', __FILE__ ) );

	wp_enqueue_script( 'angularjs', plugins_url( 'angular.min.js', __FILE__ ), array(), '1.3.8' );
	wp_enqueue_script( 
		'ranklist-module', 
		plugins_url( 'ranklist-module.js', __FILE__ ), 
		array( 'angularjs' ) );




	$upload_dir = wp_upload_dir();
	$directory = $upload_dir['baseurl'] . '/lu_rank_list/';

	wp_localize_script( 
		'ranklist-module', 
		'ranklist_data', 
		array(
			'file'        => get_option('ranklist_file'),
			'upload_path' => $directory ) );
});


/**
 * Update ranklist on cron call
 */
add_action( 'cr_update_ranklist', function(){
	$ranklist = get_option('cr_coders', array());
	$formula = get_option('cr_formula', array());

	foreach( $ranklist as $i => $coder ){
		$cf_rating = cr_get_user_points( $coder['cf_handle'], 'codeforces' );
		$tc_rating = cr_get_user_points( $coder['tc_handle'], 'topcoder' );

		$equation = strtr( $formula, array(
			'{CFR}'  => $cf_rating,
			'{TCR}'  => $tc_rating,
			'{CP}'   => $coder['class_performance'] ) );

		$lu_points = eval( "return $equation;" );

		$ranklist[$i]['cfr'] = $cf_rating;
		$ranklist[$i]['tcr'] = $tc_rating;
		$ranklist[$i]['lup'] = $lu_points;
	}

	// Convert data to json for storage
	$json_data = json_encode( $ranklist );

	$upload_dir = wp_upload_dir();
	$directory = $upload_dir['basedir'] . '/lu_rank_list/';
	$existing_file = get_option( 'ranklist_file', '' );
	if( $existing_file != '' ){
		$existing_file = $directory.$existing_file;
		$old_ranklist = json_decode( file_get_contents( $existing_file ) );
		$new_ranklist = json_decode( $json_data );
		$has_file = true;
	}
	else {
		$has_file = false;
	}

	if( !$has_file || $old_ranklist != $new_ranklist ){

		if( !file_exists( $directory ) ) mkdir( $directory );

		$filename = 'rank_list_'.date( "Ymd\\THis", current_time('timestamp') ).'.json';
		file_put_contents( $directory.$filename, $json_data );
		update_option( 'ranklist_file', $filename );

	}
});

add_shortcode( 'ranklist', function(){
	ob_start();
	?>
	<div ng-app="Ranklist">
		<table class="table" ng-controller="RanklistCtrl" style="width:100%">
			<thead>
				<tr>
					<th>Rank</th>
					<th>Name</th>
					<th>ID</th>
					<th>TopCoder</th>
					<th>Codeforces</th>
					<th>Rating</th>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="coder in list">
					<td>{{index($index)}}</td>
					<td>{{coder.name}}</td>
					<td>{{coder.sid}}</td>
					<td>
						<b>
							<a 
								ng-href="http://www.topcoder.com/member-profile/{{coder.tc_handle}}/algorithm/"
								ng-style="{color: colorCode( 'tc', coder.tcr )}"
								target="_blank">
								{{coder.tc_handle}}
							</a>
						</b>
					</td>
					<td>
						<b>
							<a 
								ng-href="http://codeforces.com/profile/{{coder.cf_handle}}"
								ng-style="{color: colorCode( 'cf', coder.cfr )}"
								target="_blank">
								{{coder.cf_handle}}
							</a>
						</b>
					</td>
					<td>{{coder.lup | number : 2}}</td>
				</tr>
			</tbody>
		</table>
	</div>
	<?php
	return ob_get_clean();
});

function cr_get_info_array( $handle, $judge ){
	if('topcoder' == $judge) $url = str_replace('{handle}', $handle, 'https://api.topcoder.com/v2/users/{handle}/statistics/data/srm');
	elseif('codeforces' == $judge) $url = str_replace('{handle}', $handle, 'http://codeforces.com/api/user.rating?handle={handle}');
	else return false;
	
	$data = wp_remote_get($url);
	if(!is_wp_error($data)){
		$json = $data['body'];
		if($json) return json_decode($json);
	}
	return false;
}

function cr_get_user_points( $handle, $judge ){
	$info = cr_get_info_array( $handle, $judge );
	if(!$info) return 0;

	if('topcoder' == $judge){
		$rating = $info->rating;
		$last_contest_date = strtotime($info->mostRecentEventDate);
	}
	elseif('codeforces' == $judge){
		$latest = array_pop($info->result);
		$rating = $latest->newRating;
		$last_contest_date = $latest->ratingUpdateTimeSeconds;
	}
	
	$value_date = mktime(0,0,0,date('m')-1,date('d'),date('Y'));
	if($last_contest_date<$value_date) $rating = 0;
	return $rating;
}