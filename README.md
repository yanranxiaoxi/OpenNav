# OpenNav



## 伪静态

### Caddy2

```caddyfile
opennav.example.com {
    tls ./cert/opennav.example.com.pem ./cert/opennav.example.com.key

	# This setting may have compatibility issues with some browsers
	# (e.g., attachment downloading on Firefox). Try disabling this
	# if you encounter issues.
	encode zstd gzip

	# Local PHP site
	root * ./web/opennav.example.com/
	php_fastcgi 10.88.0.1:9000
	file_server

	header {
		# Enable HTTP Strict Transport Security (HSTS)
		Strict-Transport-Security "max-age=63072000; includeSubDomains; preload"
		# Enable cross-site filter (XSS) and tell browser to block detected attacks
		X-XSS-Protection "1; mode=block"
		# Disallow robots indexed this site
		#X-Robots-Tag "none"
		# Server name removing
		-Server
	}

	@matchpath {
		not path /
		not path /favicon.ico
		not path /index.php*
		not path /node_modules/*
		not path *static/*
	}
    handle @matchpath {
        respond "Permission denied!" 403
    }
}
```

