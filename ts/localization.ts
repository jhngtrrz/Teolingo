// -*- js -*-
/* 2013 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk */

// Localized strings for a particular Emdros database
interface Localization {
    dbdescription : string;
    dbcopyright : string;
    emdrosobject : {
        [objType : string] : { 
            _objname : string;
            [featureValue : string] : string;
        };
    };
    emdrostype : {
        [enumType : string] : {
            [enumValue : string] : string;
        };
    };
    grammargroup? : {
        [objType : string] : {
            [groupValue : string] : string; 
        }; 
    };
    grammarfeature? : {
        [objType : string] : {
            [featureValue : string] : string; 
        }; 
    };
    grammarmetafeature? : {
        [objType : string] : {
            [featureValue : string] : string; 
        }; 
    };
    grammarsubfeature? : {
        [objType : string] : {
            [featureName : string] : {
                [featureValue : string] : string; 
            }; 
        }; 
    };
    universe : {
        [objType : string] : {
            _label : string;
            [featurevalue : string] : string; 
        }; 
    };
}

function getObjectFriendlyName(otype : string) : string {
    if (otype==='Patriarch') // Shouldn't happen
        return otype;
    var fn : string = l10n.emdrosobject[otype]._objname;
    return fn ? fn : otype;
}

function getObjectShortFriendlyName(otype : string) : string {
    if (l10n.emdrosobject[otype + '_abbrev']===undefined)
        return getObjectFriendlyName(otype);
    else
        return l10n.emdrosobject[otype + '_abbrev']._objname;
}


function getFeatureFriendlyName(otype : string, feature : string) : string
{
    if (feature==='visual')
        return localize('visual');

    var fn : string = l10n.emdrosobject[otype][feature];
    return fn ? fn : feature;
}


function getFeatureValueFriendlyName(featureType : string, value : string, abbrev : boolean) : string {
    if (abbrev && l10n.emdrostype[featureType + '_abbrev']!==undefined)
        // TODO: We assume there is no "list of " types here
        return l10n.emdrostype[featureType + '_abbrev'][value];
    

    // TODO: For now we handle "list of ..." here. Is this OK with all the other locations where this is used?
    if (featureType.substr(0,8)==='list of ') {
        featureType = featureType.substr(8); // Remove "list of "
        value = value.substr(1,value.length-2); // Remove parenteses
        if (value.length==0)
            return l10n.emdrostype[featureType]['NA'];

        var verb_classes : string[] = value.split(',');
        var localized_verb_classes : string[] = [];

        for (var ix in verb_classes)
            localized_verb_classes.push(l10n.emdrostype[featureType][verb_classes[+ix]]);
                
        localized_verb_classes.sort();
        return localized_verb_classes.join(', ');
    }

    return l10n.emdrostype[featureType][value]; // TODO Distinguish between friendly name A and S (Westminster)
}


declare var l10n : Localization;
