#!/usr/bin/perl

#smtpauth
#called by exim to verify if an smtp user is allowed to
#send email through the server
#possible success:
# user is in /etc/virtual/domain.com/passwd and password matches
# user is in /etc/passwd and password matches in /etc/shadow

sub smtpauth {
	$username	= Exim::expand_string('$1');
	$password	= Exim::expand_string('$2');
	$domain		= "";
	$unixuser	= 1;

	if ($username =~ /\@/)
	{
		$unixuser = 0;
		($username,$domain) = split(/\@/, $username);
		if ($domain eq "") { return "no"; }
	}

	if ($unixuser == 1)
	{
		#the username passed doesn't have a domain, so its a system account
		$homepath = (getpwnam($username))[7];
		if ($homepath eq "") { return 0; }
		open(PASSFILE, "< $homepath/.shadow") || return "no";
		$crypted_pass = <PASSFILE>;
		close PASSFILE;

		if ($crypted_pass eq crypt($password, $crypted_pass)) { return "yes"; }
		else { return "no"; }
	}
	else
	{
		#the username contain a domain, which is now in $domain.
		#this is a pure virtual pop account.

		open(PASSFILE, "< /etc/virtual/$domain/passwd") || return "no";
		while (<PASSFILE>)
		{
			($test_user,$test_pass) = split(/:/,$_);
			$test_pass =~ s/\n//g; #snip out the newline at the end
			if ($test_user eq $username)
			{
				if ($test_pass eq crypt($password, $test_pass))
				{
					close PASSFILE;
					return "yes";
				}
			}
		}
		close PASSFILE;
		return "no";
	}

	return "no";
}
