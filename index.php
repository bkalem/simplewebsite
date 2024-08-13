<?php
// Obtenir un token IMDSv2
$token_url = 'http://169.254.169.254/latest/api/token';
$ch = curl_init($token_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_PUT, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'X-aws-ec2-metadata-token-ttl-seconds: 21600',
    'Expect:' // Désactiver l'en-tête 'Expect: 100-continue'
));
$token = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$public_ip = false;
$instance_id = false;
$availability_zone = false; // Initialiser en cas d'échec

if ($http_code == 200 && $token !== false) {
    // Utiliser le token pour récupérer l'adresse IP publique
    $metadata_url_ip = 'http://169.254.169.254/latest/meta-data/public-ipv4';
    $ch = curl_init($metadata_url_ip);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-aws-ec2-metadata-token: ' . $token));
    $public_ip = curl_exec($ch);
    curl_close($ch);

    // Utiliser le token pour récupérer l'instance-id
    $metadata_url_id = 'http://169.254.169.254/latest/meta-data/instance-id';
    $ch = curl_init($metadata_url_id);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-aws-ec2-metadata-token: ' . $token));
    $instance_id = curl_exec($ch);
    curl_close($ch);

    // Utiliser le token pour récupérer la zone de disponibilité
    $metadata_url_az = 'http://169.254.169.254/latest/meta-data/placement/availability-zone';
    $ch = curl_init($metadata_url_az);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-aws-ec2-metadata-token: ' . $token));
    $availability_zone = curl_exec($ch);
    curl_close($ch);
} else {
    // Affichage de l'erreur si la requête du token échoue
    $public_ip = "Erreur: Impossible d'obtenir le token IMDSv2. HTTP Code: $http_code";
    $instance_id = "Erreur: Impossible d'obtenir le token IMDSv2. HTTP Code: $http_code";
    $availability_zone = "Erreur: Impossible d'obtenir le token IMDSv2. HTTP Code: $http_code";
}

// Vérification d'erreur pour la récupération de l'adresse IP publique
if ($public_ip === false) {
    $error = error_get_last();
    $public_ip = "Erreur: Impossible de récupérer l'adresse IP publique. " . ($error ? $error['message'] : '');
}

// Vérification d'erreur pour la récupération de l'instance-id
if ($instance_id === false) {
    $error = error_get_last();
    $instance_id = "Erreur: Impossible de récupérer l'instance-id. " . ($error ? $error['message'] : '');
}

// Vérification d'erreur pour la récupération de la zone de disponibilité
if ($availability_zone === false) {
    $error = error_get_last();
    $availability_zone = "Erreur: Impossible de récupérer la zone de disponibilité. " . ($error ? $error['message'] : '');
}
?>

<!DOCTYPE HTML>

<html>
	<head>
		<title>Bilal Kalem Demo Website</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="stylesheet" href="main.css" />
	</head>
	<body>
			<div id="wrapper" class="divided">
					<section class="banner style1 orient-left content-align-left image-position-right fullscreen onload-image-fade-in onload-content-fade-right">
						<div class="content">
							<h1>An Apache-hosted webpage</h1>
              <p>This is a website, used by <i>Bilal Kalem</i> in his AWS Training and Certification courses. Welcome!</p>
              <p>Mon adresse IP publique est : <strong><?php echo $public_ip; ?></strong></p>
              <p>Ma Availability Zone est : <strong><?php echo $availability_zone; ?></strong></p>							
              <p>Mon instance-id est : <strong><?php echo $instance_id; ?></strong></p>
							<ul class="actions stacked">
								<li><a href="#" class="button large wide smooth-scroll-middle">Hosted on Amazon EC2</a></li>
                                
							</ul>
						</div>
						<div class="image">
							<img src="banner.jpg" alt="" />
						</div>
					</section>  
			</div>
	</body>
</html>
