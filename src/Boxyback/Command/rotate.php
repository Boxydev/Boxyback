#!/usr/bin/env bash

for i in {0..7}; do
        index=$(date +"%Y%m%d" -d "-$i day");
        value=$(date +"%Y-%m-%d" -d "-$i day");
        keep[index]=$value; 
done

for i in {0..4}; do
        index=$(date +"%Y%m%d" -d "sunday-$((i+1)) week");
        value=$(date +"%Y-%m-%d" -d "sunday-$((i+1)) week");
        keep[index]=$value;
done

for i in {0..12}; do
        DW=$(($(date +%-W)-$(date -d $(date -d "$(date +%Y-%m-15) -$i month" +%Y-%m-01) +%-W)))
        T=$(date -d "$(date +%Y-%m-15) -$i month" +%Y)
        for (( AY=T; AY < $(date +%Y); AY++ ));
        do
                ((DW+=$(date -d $AY-12-31 +%W)))
        done
        index=$(date +"%Y%m%d" -d "sunday-$DW weeks");
        value=$(date +"%Y-%m-%d" -d "sunday-$DW weeks");
        keep[index]=$value; 
done

for i in {0..5}; do
        DW=$(date +%-W)
        T=$(($(date +%Y)-i))
        for (( AY=T; AY < $(date +%Y); AY++ )); do
                ((DW+=$(date -d $AY-12-31 +%W)))
        done
        index=$(date +"%Y%m%d" -d "sunday-$DW weeks");
        value=$(date +"%Y-%m-%d" -d "sunday-$DW weeks");
        keep[index]=$value;
done

#echo ${keep[@]}

ls $1 | grep -vE "$(IFS=\| && echo "${keep[*]}")"
#| xargs -r rm