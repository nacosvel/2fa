<a id="readme-top"></a>

# Nacosvel Authenticator

This community-driven PHP extension package provides secure one-time password (OTP) generation, implementing both HOTP (RFC 4226) and TOTP (RFC 6238) algorithms. Designed for reliability and interoperability, it ensures seamless compatibility with Google Authenticator and other industry-standard OTP solutions, making it ideal for implementing two-factor authentication (2FA) in PHP applications.

[![GitHub Tag][GitHub Tag]][GitHub Tag URL]
[![Total Downloads][Total Downloads]][Packagist URL]
[![Packagist Version][Packagist Version]][Packagist URL]
[![Packagist PHP Version Support][Packagist PHP Version Support]][Repository URL]
[![Packagist License][Packagist License]][Repository URL]

<!-- TABLE OF CONTENTS -->
<details>
    <summary>Table of Contents</summary>
    <ol>
        <li><a href="#installation">Installation</a></li>
        <li><a href="#usage">Usage</a></li>
        <li><a href="#contributing">Contributing</a></li>
        <li><a href="#contributors">Contributors</a></li>
        <li><a href="#license">License</a></li>
    </ol>
</details>

<!-- INSTALLATION -->

## Installation

You can install the package via [Composer]:

```bash
composer require nacosvel/2fa
```

<p align="right">[<a href="#readme-top">back to top</a>]</p>

<!-- USAGE EXAMPLES -->

## Usage

### Generate Secret

```php
use Nacosvel\Authenticator\Authentication;

$secret = Authentication::generateSecret(20, false);
// string(32) "RIPPSUOU3EQBR3FXML2QL43SRYGWCKY3"
```

### Generate Token

```php
use Nacosvel\Authenticator\HOTP;
use Nacosvel\Authenticator\TOTP;

$hotpToken = HOTP::generateToken($secret, 30, 6, 'sha1');
$totpToken = TOTP::generateToken($secret, 30, 6, 'sha1');
// string(6) "577349"
// string(6) "981726"
```

### Validate Token

```php
use Nacosvel\Authenticator\HOTP;
use Nacosvel\Authenticator\TOTP;

$hotpValidate = HOTP::validate($secret, $hotpToken, 30, 6, 'sha1', 3);
$totpValidate = TOTP::validate($secret, $totpToken, 30, 6, 'sha1', 3);
// bool(true)
// bool(true)
```

### Generate URI

```php
use Nacosvel\Authenticator\HOTP;
use Nacosvel\Authenticator\TOTP;

$hotpURI = HOTP::buildURI($secret, 'Mr.Alex', 'Github', 30, 6, 'sha1')->toString();
$totpURI = TOTP::buildURI($secret, 'Mr.Alex', 'Github', 30, 6, 'sha1')->toString();
// string(118) "otpauth://hotp/Github:Mr.Alex?secret=LWN4DKX2KHW4X7VMSWNIRJVHW4F4SQ4Z&counter=30&digits=6&algorithm=sha1&issuer=Github"
// string(117) "otpauth://totp/Github:Mr.Alex?secret=RIPPSUOU3EQBR3FXML2QL43SRYGWCKY3&period=30&digits=6&algorithm=sha1&issuer=Github"
```

### URI::fromString

```php
use Nacosvel\Authenticator\URI;

$uri = URI::fromString('otpauth://totp/Github:Mr.Alex?secret=RIPPSUOU3EQBR3FXML2QL43SRYGWCKY3&period=30&digits=6&algorithm=sha1&issuer=Github');
var_dump([
    'type'      => $uri->getType(),     // totp
    'issuer'    => $uri->getIssuer(),   // Github
    'account'   => $uri->getAccount(),  // Mr.Alex
    'secret'    => $uri->getSecret(),   // RIPPSUOU3EQBR3FXML2QL43SRYGWCKY3
    'digits'    => $uri->getDigits(),   // 6
    'algorithm' => $uri->getAlgorithm(),// sha1
]);
```

### URI::buildURI

```php
use Nacosvel\Authenticator\URI;
use Nacosvel\Authenticator\HOTP;
use Nacosvel\Authenticator\TOTP;

$hotpURI = URI::buildURI(HOTP::class, 'Mr.Alex', 'Github')->secret($secret)->toString();
$totpURI = URI::buildURI(TOTP::class, 'Mr.Alex', 'Github')->secret($secret)->toString();
// string(83) "otpauth://hotp/Github:Mr.Alex?secret=LWN4DKX2KHW4X7VMSWNIRJVHW4F4SQ4Z&issuer=Github"
// string(83) "otpauth://totp/Github:Mr.Alex?secret=RIPPSUOU3EQBR3FXML2QL43SRYGWCKY3&issuer=Github"
```

<!-- CONTRIBUTING -->

## Contributing

Contributions are what make the open source community such an amazing place to learn, inspire, and create. Any contributions you make are **greatly appreciated**.

If you have a suggestion that would make this better, please fork the repo and create a pull request. You can also simply open an issue with the tag "enhancement".
Don't forget to give the project a star! Thanks again!

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

<p align="right">[<a href="#readme-top">back to top</a>]</p>

<!-- CONTRIBUTORS -->

## Contributors

Thanks goes to these wonderful people:

<a href="https://github.com/nacosvel/2fa/graphs/contributors">
  <img src="https://contrib.rocks/image?repo=nacosvel/2fa" alt="contrib.rocks image" />
</a>

Contributions of any kind are welcome!

<p align="right">[<a href="#readme-top">back to top</a>]</p>

<!-- LICENSE -->

## License

Distributed under the MIT License (MIT). Please see [License File] for more information.

<p align="right">[<a href="#readme-top">back to top</a>]</p>

[GitHub Tag]: https://img.shields.io/github/v/tag/nacosvel/2fa

[Total Downloads]: https://img.shields.io/packagist/dt/nacosvel/2fa?style=flat-square

[Packagist Version]: https://img.shields.io/packagist/v/nacosvel/2fa

[Packagist PHP Version Support]: https://img.shields.io/packagist/php-v/nacosvel/2fa

[Packagist License]: https://img.shields.io/github/license/nacosvel/2fa

[GitHub Tag URL]: https://github.com/nacosvel/2fa/tags

[Packagist URL]: https://packagist.org/packages/nacosvel/2fa

[Repository URL]: https://github.com/nacosvel/2fa

[GitHub Open Issues]: https://github.com/nacosvel/2fa/issues

[Composer]: https://getcomposer.org

[License File]: https://github.com/nacosvel/2fa/blob/main/LICENSE
