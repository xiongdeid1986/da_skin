<domain>
    <get>
        <filter>
            <?php foreach($domains as $domain): ?>
            <name><?php echo $domain; ?></name>
            <?php endforeach; ?>
        </filter>
        <dataset>
            <limits/>
            <stat/>
            <gen_info/>
        </dataset>
    </get>
</domain>