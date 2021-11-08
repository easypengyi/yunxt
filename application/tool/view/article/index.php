<?php isset($content) OR $content = ''; ?>
<?php isset($title) OR $title = ''; ?>

<style type="text/css">
    body {
        margin: 0;
        padding: 0;
    }

    .Graphic {
        width: 100%;
        float: left;
    }

    .Graphic p {
        width: 100%;
        float: left;
        border: none;
        margin: 0 0 0.1rem 0;
        padding: 0;
    }

    .Graphic img {
        width: 100%;
        float: left;
    }

    .Graphic video {
        width: 100%;
        float: left;
    }
</style>

<!--suppress JSValidateTypes -->
<script>
    if (document.title === '') {
        document.title = '<?php echo $title ?>';
    }
</script>

<div class="Graphic">
    <?php echo $content ?>
</div>
