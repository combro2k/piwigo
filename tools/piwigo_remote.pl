#!/usr/bin/perl

use strict;
use warnings;

use JSON;
use LWP::UserAgent;
use Getopt::Long;

my %opt = ();
GetOptions(
    \%opt,
    qw/action=s file=s thumbnail=s categories=s define=s%/
);

our $ua = LWP::UserAgent->new;
$ua->cookie_jar({});

my %conf;
$conf{base_url} = 'http://localhost/~pierrick/piwigo/trunk';
$conf{response_format} = 'json';
$conf{username} = 'pierrick';
$conf{password} = 'z0rglub';
$conf{limit} = 10;

my $result = undef;
my $query = undef;

binmode STDOUT, ":encoding(utf-8)";

# TODO : don't connect at each script call, use the session duration instead.
my $form = {
    method => 'pwg.session.login',
    username => $conf{username},
    password => $conf{password},
};

$result = $ua->post(
    $conf{base_url}.'/ws.php?format=json',
    $form
);

# print "\n", $ua->cookie_jar->as_string, "\n";

if ($opt{action} eq 'pwg.images.add') {
    use MIME::Base64 qw(encode_base64);
    use Digest::MD5::File qw/file_md5_hex/;
    use File::Slurp;

    my $file_content = encode_base64(read_file($opt{file}));
    my $file_sum = file_md5_hex($opt{file});

    my $thumbnail_content = encode_base64(read_file($opt{thumbnail}));
    my $thumbnail_sum = file_md5_hex($opt{thumbnail});

    $form = {
        method => 'pwg.images.add',
        file_sum => $file_sum,
        file_content => $file_content,
        thumbnail_sum => $thumbnail_sum,
        thumbnail_content => $thumbnail_content,
        categories => $opt{categories},
    };

    foreach my $key (keys %{ $opt{define} }) {
        $form->{$key} = $opt{define}{$key};
    }

    my $response = $ua->post(
        $conf{base_url}.'/ws.php?format=json',
        $form
    );

    print "-" x 50, "\n";
    printf("response code    : %u\n", $response->code);
    printf("response message : %s\n", $response->message);
    print "-" x 50, "\n";
    print "\n";

#     use Data::Dumper;
#     print Dumper($response);

    if ($response->is_success) {
        print "upload successful\n";
    }
    else {
        warn 'A problem has occured during upload', "\n";
        warn $response->decoded_content, "\n";
        die $response->status_line;
    }
}

if ($opt{action} eq 'pwg.tags.list') {
    use Text::ASCIITable;

    $query = pwg_ws_get_query(
        method => 'pwg.tags.getList',
        sort_by_counter => 'true',
    );

    $result = $ua->get($query);
    my $tag_result = from_json($result->content);
    my $t = Text::ASCIITable->new({ headingText => 'Tags' });
    $t->setCols('id','counter','name');

    my $tag_number = 1;
    foreach my $tag_href (@{ $tag_result->{result}{tags} }) {
        $t->addRow(
            $tag_href->{id},
            $tag_href->{counter},
            $tag_href->{name}
        );

        last if $tag_number++ >= $conf{limit};
    }
    print $t;
}

$query = pwg_ws_get_query(
    method => 'pwg.session.logout'
);
$ua->get($query);

sub pwg_ws_get_query {
    my %params = @_;

    my $query = $conf{base_url}.'/ws.php?format='.$conf{response_format};

    foreach my $key (keys %params) {
        $query .= '&'.$key.'='.$params{$key};
    }

    return $query;
}
