<?php
/*
Plugin Name: Trigger Gitlab CI
Version: 1.0
Description: This plugin triggers GitLab pipeline for a specific branch.
*/

if (is_admin()) {
    add_action('admin_menu', 'glci_admin_menu');
}

function glci_init() {
    echo "<div class='wrap'><h1>Trigger CI</h1>";

    if (isset($_GET['action']) && $_GET['action'] == 'trigger') {
        $res = json_decode(trigger_pipeline($_GET['env']));
        if ($res->status) {
            echo "<br/>Update triggered";
            echo "<br/>Update status: <a href='".$res->web_url."'>link</a>";
        } else {
            echo "<br/>An error occurred<br/>";
            print_r($res);
        }

    } else {
        echo '<form action="admin.php" method="GET">';
        echo '<input type="hidden" name="page" value="glci-update"/>';
        echo '<input type="hidden" name="action" value="trigger"/>';
        echo '<table>';
        echo '<tr><td>Branch:</td><td><select name="env">';
        
        // Fetching available branches from options
        $branches = get_option('glci_branches');
        if (!empty($branches)) {
            foreach ($branches as $branch) {
                echo "<option value='$branch'>$branch</option>";
            }
        } else {
            // Default option
            echo '<option value="master">master</option>';
        }
        
        echo '</select></td>';
        echo '<td><button class="button-primary">Trigger</button></td></tr>';
        echo '</table></form>';
    }
    echo "</div>";
}

function glci_admin_menu() {
    add_menu_page( 'Trigger CI', 'Trigger CI', 'manage_options', 'glci-update', 'glci_init', '', 1000);
    add_submenu_page('glci-update', 'Settings', 'Settings', 'manage_options', 'glci-settings', 'glci_settings_page');
}

function glci_settings_page() {
    if (isset($_POST['submit'])) {
        // Saving GitLab token, project ID, and available branches to options
        update_option('glci_token', $_POST['token']);
        update_option('glci_project_id', $_POST['project_id']);
        $branches = !empty($_POST['branches']) ? explode(',', $_POST['branches']) : [];
        update_option('glci_branches', $branches);
        echo '<div class="updated"><p>Settings saved.</p></div>';
    }

    $token = get_option('glci_token', '');
    $project_id = get_option('glci_project_id', '');
    $branches = get_option('glci_branches', []);
    $branches_str = implode(',', $branches);

    echo '<div class="wrap">';
    echo '<h1>GitLab CI Settings</h1>';
    echo '<form method="post" action="">';
    echo '<table class="form-table">';
    echo '<tr>';
    echo '<th scope="row"><label for="token">GitLab Token:</label></th>';
    echo '<td><input type="text" id="token" name="token" value="' . esc_attr($token) . '"></td>';
    echo '</tr>';
    echo '<tr>';
    echo '<th scope="row"><label for="project_id">Project ID:</label></th>';
    echo '<td><input type="text" id="project_id" name="project_id" value="' . esc_attr($project_id) . '"></td>';
    echo '</tr>';
    echo '<tr>';
    echo '<th scope="row"><label for="branches">Available Branches (comma-separated):</label></th>';
    echo '<td><input type="text" id="branches" name="branches" value="' . esc_attr($branches_str) . '"></td>';
    echo '</tr>';
    echo '</table>';
    echo '<input type="submit" name="submit" class="button button-primary" value="Save Settings">';
    echo '</form>';
    echo '</div>';
}

function executeCurl($url, $token, $post = false, $data = null) {
    $ch = curl_init($url);

    $headers = ($token) ? ["PRIVATE-TOKEN: $token"] : [];
    if ($post) {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }

    if ($headers) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $res = curl_exec($ch);
    curl_close($ch);

    return $res;
}

function trigger_pipeline($ref = "master", $token = "") {
    // Fetching token and project ID from options
    $token = get_option('glci_token');
    $project_id = get_option('glci_project_id');
    $res = executeCurl("https://gitlab.com/api/v4/projects/$project_id/ref/{$ref}/trigger/pipeline?token={$token}", null, true);

    return $res;
}

