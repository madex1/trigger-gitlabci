# Trigger Gitlab CI Wordpress Plugin

## 1. What does this plugin do?

The Gitlab CI Trigger plugin enables seamless integration between your WordPress backend and your GitLab CI pipelines. With this plugin, you can trigger GitLab pipeline runs directly from your WordPress dashboard, making it easy to automate site updates and deployments.

## 2. For whom?

This plugin is ideal for users who utilize WordPress as a backend for static site generation using platforms like Astro, Gatsby, Hugo, or similar tools. If you're managing your site's content in WordPress but generating the static site and deploying it via GitLab CI pipelines, this plugin streamlines the process by allowing you to trigger pipeline runs without leaving the WordPress environment.

## 3. Steps to install:

1. Download the plugin ZIP file from the Releases section or clone the repository.
2. Upload the plugin directory to your WordPress plugins directory (wp-content/plugins/).
3. Activate the plugin through the WordPress admin panel.

## 4. Steps to configure:

1. After activation, navigate to the "GitLab CI Settings" page under the "Settings" menu in the WordPress admin panel.
2. Enter your GitLab token and project ID.
3. Specify the available branches for triggering pipelines (comma-separated).
4. Save the settings.

That's it! You're now ready to trigger GitLab CI pipelines directly from your WordPress dashboard.