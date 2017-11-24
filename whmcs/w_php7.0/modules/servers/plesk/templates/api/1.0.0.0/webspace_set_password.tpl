<domain>
    <set>
        <filter>
            <name><?php echo $domain; ?></name>
        </filter>
        <values>
            <hosting>
                <vrt_hst>
                    <ftp_password><?php echo $password; ?></ftp_password>
                </vrt_hst>
            </hosting>
        </values>
    </set>
</domain>