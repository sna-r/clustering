
<style>
    body {
        margin: 0; /* Remove default margin */
        font-family: Arial, sans-serif; /* Font style */
        background-color: #f0f0f0; /* Background color */
    }

    .teext-center {
        display: flex;
        justify-content: center; /* Center horizontally */
        align-items: center; /* Center vertically */
        height: 100vh; /* Full height of the viewport */
    }

    h1 {
        font-size: 48px; /* Large font size */
        color: #333; /* Text color */
    }

    .mb-3 {
        margin-bottom: 1rem; /* Margin between elements */
    }

    .btn-primary {
        background-color: #007bff; /* Bootstrap-style button color */
        color: #fff; /* Text color */
        padding: 0.5rem 1rem; /* Padding for the button */
        text-decoration: none; /* Remove underline */
        text-align: center; /* Center text in the button */
        display: inline-block; /* Inline-block button */
        border: none; /* No border */
        border-radius: 4px; /* Rounded corners */
        cursor: pointer; /* Pointer cursor */
    }

    .btn-primary:hover {
        background-color: #0056b3; /* Darker shade for hover */
    }
</style>

<div class="teext-center">
    <div>
        <div class="mb-3">
            <h1>Hello, Apache2 Server</h1>
        </div>
        <div class="mb-3">
            <a class="btn-primary" name="logout" href="../util/logout.php">Logout</a>
        </div>
    </div>
</div>
    