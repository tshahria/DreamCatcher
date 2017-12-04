import cv2 # OpenCV
import sys
import pprint
# Input image
image_path = sys.argv[1]

# Model parameters
dir_path = "" # Please modify this for your environment
filename = "faces.xml" # for frontal faces
#filename = "haarcascade_profileface.xml" # for profile faces
model_path = filename

# Create the classifier
clf = cv2.CascadeClassifier(model_path)

# Read the image
image = cv2.imread(image_path)
gray = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)
height = image.shape[0]
width = image.shape[1]
size = height * width

if size > (500^2):            
	r = 500.0 / image.shape[1]        
	dim = (500, int(image.shape[0] * r))        
	img2 = cv2.resize(image, dim, interpolation = cv2.INTER_AREA)        
	image = img2
# Detect faces on image
faces = clf.detectMultiScale(
    gray,
    scaleFactor=1.1,
    minNeighbors=3,
    minSize=(20, 20),
    flags=cv2.CASCADE_SCALE_IMAGE
)

print("Found {0} faces!".format(len(faces)))

# Draw a rectangle around the faces
for (x, y, w, h) in faces:
	cv2.rectangle(image, (x, y), (x+w, y+h), 1)

cv2.imshow("Faces found", image)
cv2.waitKey(0)