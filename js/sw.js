console.log('Started', self);
self.addEventListener('install', function(event) {
  self.skipWaiting();
  console.log('Installed', event);
});
self.addEventListener('activate', function(event) {
  console.log('Activated', event);
});
self.addEventListener('push', function(event) {
  console.log('Push message received', event);
  // TODO
});

//KEY
//AIzaSyDGvAG9cuF-yYjNd6vrSe54dsV9YnLV_Jo
//PROJECT NUMBER
//664025214915
//ENDPOINT
//d5jgKBCl7NE:APA91bF7NTKyxHNnrmWjJG1…B0yh7gFvkWKCt3ZtW23xxdBkPjvepPL_WvoLDrZ7wMI3cNLs_1o0eAIiPdRFI3v2op2ndqYFYn