import numpy as np
import cv2
import os, os.path
from matplotlib import pyplot as plt
import time

#multiple cascades: https://github.com/Itseez/opencv/tree/master/data/haarcascades

#https://github.com/Itseez/opencv/blob/master/data/haarcascades/haarcascade_frontalface_default.xml
face_cascade = cv2.CascadeClassifier('faces.xml')
#https://github.com/Itseez/opencv/blob/master/data/haarcascades/haarcascade_eye.xml
eye_cascade = cv2.CascadeClassifier('eye.xml')

DIR = 'input'
numPics = len([name for name in os.listdir(DIR) if os.path.isfile(os.path.join(DIR, name))])
print([name for name in os.listdir(DIR) if os.path.isfile(os.path.join(DIR, name))])
n=1
for pic in os.listdir(DIR):
    if (pic == '.DS_Store'):
        print(pic)
    else:
        print(pic)    
        img = cv2.imread('input/'+pic)
        height = img.shape[0]
        #print("height: " + str(height))
        width = img.shape[1]
        size = height * width

        if size > (500^2):
            r = 500.0 / img.shape[1]
            dim = (500, int(img.shape[0] * r))
            img2 = cv2.resize(img, dim, interpolation = cv2.INTER_AREA)
            img = img2

        gray = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)
        faces = face_cascade.detectMultiScale(
            gray,
            scaleFactor=1.35,
            minNeighbors=4,
            minSize=(60, 60),
            flags=cv2.CASCADE_SCALE_IMAGE)
        k = 0

        for (x,y,w,h) in faces:
            imgCrop = img[y:y+h,x:x+w]
            #cv2.rectangle(img,(x,y),(x+w,y+h),(255,0,0),2)
            roi_gray = gray[y:y+h, x:x+w]
            roi_color = img[y:y+h, x:x+w]

            #eyes = eye_cascade.detectMultiScale(roi_gray)

            #cv2.imshow('img',img)
            cv2.imwrite("output/crop"+str(n)+"_"+str(k)+".jpg", imgCrop)
            print(str(pic)+" has been processed and cropped")
            #time.sleep(5)   
        n += 1

        #print(str(pic)+" has been processed and cropped")
        k = cv2.waitKey(30) & 0xff
        if k == 27:
            break

#cap.release()
print("All images have been processed!!!")
cv2.destroyAllWindows()
cv2.destroyAllWindows()