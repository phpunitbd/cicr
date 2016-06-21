#!/usr/bin/perl
# find app/code/local/Data -name \*.php > /tmp/l1
# find app/design/*/default/icrc/ -name \*.phtml >> /tmp/l1
# xargs < /tmp/l1 perl scripts/get_traductions.pl

my %dict;

sub readFile
{
  $file = shift;
  open IN, '<', $file or die "Cannot open file";
  while (<IN>) {
    if ($_ =~ /__\(('(.*?)'|"(.*?)")(,[^,]+)*\)/) {
      $dict{$2} = 1;
    }
  }
  close IN;
}

for (my $i = 0; $i <= $#ARGV; ++$i) {
  readFile $ARGV[$i];
}

my @scrambled;
while (($key, $value) = each %dict) {
  #print $key . ", ''\n";
  push @scrambled, $key;
}

@sorted = sort @scrambled;
foreach (@sorted) {
  if (/,/) {
    print '"', $_ . "\", \"\"\n";
  } else {
    print $_ . ",\n";
  }
}

