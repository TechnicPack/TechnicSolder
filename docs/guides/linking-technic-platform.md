# Linking Solder to the Technic Platform

Linking your Solder installation to the Technic Platform lets the Platform pull modpack and mod data directly from your Solder instance -- importing packs and generating detailed mod lists on your Platform page.

The process has three steps: copy an API key from the Platform, add it to Solder, then point the Platform back at your Solder API URL.

## Step 1: Get Your API Key from the Platform

1. Visit [technicpack.net](https://www.technicpack.net) and log in.
2. Click your name in the upper-right corner and select **Edit Profile**.
3. Click **Solder Configuration** in the sub-menu.
4. Copy the **API Key** shown on that page.

## Step 2: Add the API Key to Solder

1. Log in to your Solder installation.
2. Navigate to **Configure Solder > API Key Management**.
3. Click **Add API Key**.
4. Enter a descriptive name (e.g. your Platform username) and paste the API key you copied.
5. Click **Add Key**.

## Step 3: Link Solder on the Platform

1. Go back to the **Solder Configuration** page on the Technic Platform.
2. In the **Solder URL** field, enter the full URL to your Solder API, including the trailing `/api/`.

    ```
    https://solder.example.com/api/
    ```

3. Click **Link Solder**.

!!! warning
    The URL must include `/api/` at the end. Without it, the Platform cannot reach the Solder API and the link will fail.

If everything is configured correctly, the Technic Platform will verify your Solder installation and activate Solder support on your account.

## Verifying the Link

Once linked, you should see a confirmation message on the Platform's Solder Configuration page. You can also check that your modpacks appear in the Platform's pack management interface.

!!! tip
    If the link fails, make sure your Solder instance is publicly accessible and that your web server is correctly proxying requests to the application. See the [web server configuration guide](web-server-configs.md) for examples.
