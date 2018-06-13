#!/bin/bash

echo "Running delta imports for buyerposts"
curl --data "command=delta-import&verbose=false&clean=false&commit=true&optimize=false&core=logistiksdev&entity=buyerposts" "http://localhost:8983/solr/logistiksdev/dataimport?_=1489486020236&indent=on&wt=json"

echo "Running delta imports for sellerposts"
curl --data "command=delta-import&verbose=false&clean=false&commit=true&optimize=false&core=logistiksdev&entity=sellerposts" "http://localhost:8983/solr/logistiksdev/dataimport?_=1489486020236&indent=on&wt=json"

echo "Finished delta imports"


